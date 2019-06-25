<?php

namespace App\Http\Controllers\Api\Document\v1;


use App\Http\Controllers\Api\Document\v1;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Validator;
use App\Document;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class DocumentApiController
 * @package App\Http\Controllers\Api\Document\v1
 */
class DocumentApiController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * Refresh token or Create new user in DB
     */
    public function auth(Request $request){
        $data = json_decode($request->getContent());
        $response = 'Bad Request';
        if(!empty($data->login)){
            $token = bin2hex(openssl_random_pseudo_bytes(16));
            $current_timestamp = Carbon::now()->addHours(1)->timestamp;
            $response = (object)array('user' => $data->login, 'token' => $token, 'until' => $current_timestamp);
            DB::table('users')->where('name', $data->login)->lockForUpdate()->updateOrInsert(
                ['name' => $data->login],
                ['token' => $token, 'until' => $current_timestamp]
            );
        }
        return response()->json($response);
    }

    /**
     * @param Request $request
     * @return string
     * validate request if authorized or not:
     * published = anon
     * none = invalid token
     * username = valid user with valid token
     */
    private function validateAuth(Request $request){
        if(!empty($request->header('authorization'))) {
            $data = explode(' ', $request->header('authorization'));
            if (count($data) == 2) {
                $auth = DB::table('users')->where([
                    ['name', $data[0]],
                    ['token', $data[1]],
                    ['until', '>', Carbon::now()->timestamp],
                ])->exists();
                if($auth == TRUE){
                    return $data[0];
                }
            }
            return 'none';
        }
        return 'published';
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * create document for authorized user
     */
    public function createDocument(Request $request)
    {
        $access = $this->validateAuth($request);
        if($access == 'none' || $access == 'published'){
            return response()->json('Bad Authorization',401);
        }
        $document = new Document;
        $document->setOwner($access);
        $document->save();
        return response()->json($document->toArray());
    }

    /**
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * get document for anon or authorized user
     */
    public function getDocument($id, Request $request)
    {
        $access = $this->validateAuth($request);
        if($access == 'none'){
            return response()->json('Bad Authorization',401);
        }
        if($access == 'published'){
            $response = Document::where([
                ['id', '=', $id],
                ['status', '=', 'published'],
            ])->get();
        }
        else{
            $response = Document::where([
                ['id', '=', $id],
                ['owner', '=', $access],
            ])->orWhere([
                ['id', '=', $id],
                ['status', '=', 'published'],
            ])->get();
        }
        return $response->isNotEmpty()
            ? response()->json($response[0])
            : response()->json('Record not found',404);

    }

    /**
     * @param object $target
     * @param object $patch
     * @return object
     * patch document with rfc7396
     */
    function patcher($target, $patch)
    {
        if(!is_object($patch)){
            return $patch;
        }
        if(!is_object($target)){
            $target = (object)array();
        }
        foreach($patch as $key => $value)
        {
            if($value == null){
                unset($target->$key);
            }
            else{
                if(!isset($target->$key)){
                    $target->$key = NULL;
                }
                $target->$key = $this->patcher($target->$key, $value);
            }
        }

        return $target;
    }

    /**
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * edit document with rfc7396 patching
     * only authorized users can patch document
     */
    public function editDocument($id, Request $request)
    {
        $access = $this->validateAuth($request);
        if($access == 'none' || $access == 'published'){
            return response()->json('Bad Authorization',401);
        }
        $document = Document::where([
            ['id', '=', $id],
            ['owner', '=', $access],
        ])->lockForUpdate()->get();
        if($document->isEmpty()){
            return response()->json('Record not found',404);
        }
        if($document->first->status == 'published'){
            return response()->json('Record already published',400);
        }
        $target = json_decode($document->first()->payload);
        $patch = json_decode($request->getContent());
        if(isset($patch->document->payload)){
            $patch = $patch->document->payload;
        }
        else {
            return response()->json('Bad request',400);
        }
        if(empty($target)){
            $document[0]->payload = json_encode($patch);
        }
        else{
            $document[0]->payload = json_encode($this->patcher($target, $patch));
        }
        $document[0]->save();
        return response()->json($document[0]->toArray());
    }

    /**
     * @param string $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * only authorized users can publish document
     */
    public function publishDocument($id, Request $request)
    {
        $access = $this->validateAuth($request);
        if($access == 'none' || $access == 'published'){
            return response()->json('Bad Authorization',401);
        }
        $document = Document::where([
            ['id', '=', $id],
            ['owner', '=', $access],
        ])->lockForUpdate()->get();
        if($document->isEmpty()){
            return response()->json('Record not found',404);
        }
        if($document->first()->status == 'published'){
            return response()->json('Record already published',200);
        }

        $document[0]->status = 'published';
        $document[0]->save();
        return response()->json($document->first->toArray());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * show specific document for everyone if it publish or draft only for user
     */
    public function showDocument(Request $request)
    {
        $access = $this->validateAuth($request);
        if($access == 'none'){
            return response()->json('Bad Authorization',401);
        }

        $args = $request->query();
        $validatedData = Validator::make($args,[
            'page' => 'required|integer|gt:0',
        ]);
        if ($validatedData->fails()) {
            $args['page'] = 1;
        }
        $args['page'] = (int)$args['page'];
        $validatedData = Validator::make($args,['perPage' => 'required|integer|between:1,100']);
        if ($validatedData->fails()) {
            $args['perPage'] = 20;
        }
        $args['perPage'] = (int)$args['perPage'];

        if($access == 'published'){
            $documents = Document::where([
                ['status', '=', 'published'],
            ])->orderBy('createAt', 'desc')
                ->offset($args['perPage']*($args['page']-1))
                ->limit($args['perPage'])
                ->get();
            $total = Document::where([
                ['status', '=', 'published'],
            ])->count();
        }
        else{
            $documents = Document::where([
                ['owner', '=', $access],
            ])->orWhere([
                ['status', '=', 'published'],
            ])->orderBy('createAt', 'desc')
                ->offset($args['perPage']*($args['page']-1))
                ->limit($args['perPage'])
                ->get();
            $total = Document::where([
                ['owner', '=', $access],
            ])->orWhere([
                ['status', '=', 'published'],
            ])->count();
        }
        $response = (object)array(
            "document" => array(),
            "pagination" => (object)[
                "page" => $args['page'],
                "perPage" => $args['perPage'],
                "total" => $total],
        );

        foreach($documents as $document){
            $response->document[] = $document->fieldsToArray();
        }

        return response()->json($response);
    }
}