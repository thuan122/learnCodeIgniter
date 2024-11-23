<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectModel extends Model
{
    protected $table            = 'projects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    // Enabled this will make delete function write the row with deleted_at a timestamp
    // Instead of deleted it (need to enabled $useSoftDeletes)
    protected $useSoftDeletes   = false;
    // Prevent $allowedFields mass assignment
    protected $protectFields    = true;
    // Allowed these input can be inserted or updated
    protected $allowedFields    = [
        'user_id',
        'title',
        'budget'
    ];


    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // To convert database fields to a specific data type when they are retrieved
    // Convention: $casts = ['column_name' => 'data_type']
    protected array $casts = [];
    // Same with $casts, but for custome data type
    // Convention: $castHandlers = ['column_name' => Path to custom data type file]
    // Note: the custom data type class need to implement the EntityCastInterface type
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    // The same usage as Laravel validation
    // To validate input when send request
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    /**
     * Allow to run function before or after insert/update/find/delete
     */
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
