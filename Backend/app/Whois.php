<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Whois extends Model
{
    protected $fillable = [
        'createdDate', 'updatedDate', 'expiresDate', 'registrant', 'domainName'
    ];
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
