<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Whois extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'id', 'createdDate', 'expiresDate', 'registrant', 'domainName'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
