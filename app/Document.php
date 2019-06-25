<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class Document
 * @package App
 * model of document
 */
class Document extends Model
{
    const CREATED_AT = 'createAt';
    const UPDATED_AT = 'modifyAt';
    protected $table = 'documents';
    protected $dateFormat = 'Y-m-d\TH:iP';
    protected $hidden = ['owner'];

    /**
     * prepare model then it created
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            $document->id = (string) Str::uuid();
            $document->status = 'draft';
            $document->payload = '{}';
        });
    }

    /**
     * @return bool
     * dont increment primary key = id
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * @return array
     * specific conversion model to array
     */
    public function toArray(){
        return ['document' => parent::toArray()];
    }

    /**
     * @return array
     * convert fields to array
     */
    public function fieldsToArray(){
        return parent::toArray();
    }

    /**
     * @param $value
     * public setter for owner
     */
    public function setOwner($value){
        $this->attributes['owner'] = $value;
    }
}
