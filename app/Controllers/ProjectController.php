<?php

namespace App\Controllers;

use App\Models\ProjectModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ProjectController extends ResourceController
{
    use ResponseTrait;

    
    /**
     * Add a project 
     *
     * @return void
     */
    public function addProject()
    {
        $rules = [
            'title' => 'required',
            'budget' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return $this->respond(
                $this->genericResponse(
                    ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                    $this->validator->getErrors(),
                    true,
                    []
                )
            );
        }

        $user_id = auth()->id();
        $projectObj = new ProjectModel();

        $data = [
            "user_id" => $user_id,
            "title" => $this->request->getVar("title"),
            "budget" => $this->request->getVar("budget"),
        ];

        if ($projectObj->insert($data)) {
            return $this->respond(
                $this->genericResponse(
                    ResponseInterface::HTTP_OK,
                    "New Project created successfully",
                    false,
                    []
                )
            );
        }

        return $this->respond(
            $this->genericResponse(
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                "Failed to insert Project",
                true,
                []
            )
        );
    }

        
    /**
     * Get all project that belong to the current logged in user 
     *
     * @return void
     */
    public function listProjects()
    {
        $user_id = auth()->id();
        $projectObj = new ProjectModel();

        $projects = $projectObj->where(['user_id' => $user_id])->get()->getResultArray();

        return $this->respond(
            $this->genericResponse(
                ResponseInterface::HTTP_OK,
                "Projects found",
                false,
                ["projects" => $projects]
            )
        );
    }
    
    /**
     * Delete a project (need to be belong the the current logged in user)
     *
     * @param  mixed $project_id
     * @return void
     */
    public function deleteProject($project_id)
    {
        $user_id = auth()->id();
        $projectObj = new ProjectModel();

        $project = $projectObj->where(
            [
                "id" => $project_id,
                "user_id" => $user_id
            ]
        )->get()->getRowArray();

        if (empty($project)) {
            return $this->respond(
                $this->genericResponse(
                    ResponseInterface::HTTP_NOT_FOUND,
                    "Project not found",
                    true,
                    []
                )
            );
        }

        $projectObj->where([
            "id" => $project_id,
            "user_id" => $user_id
        ])->delete();

        return $this->respond(
            $this->genericResponse(
                ResponseInterface::HTTP_OK,
                "Project deleted",
                false,
                []
            )
        );
    }

    public function genericResponse(int $status, string|array $message, bool $error, array $data): array
    {
        return [
            "status" => $status,
            "message" => $message,
            "error" => $error,
            "data" => $data,
        ];
    }
}
