<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class AuthController extends ResourceController
{
    use ResponseTrait;
    const TOKEN_NAME = "API TOKEN";

    /**
     * Register the user
     *
     * @return void
     */
    public function register()
    {
        $rules = [
            "username" => "required|is_unique[users.username]",
            "email" => "required|is_unique[auth_identities.secret]",
            "password" => "required"
        ];

        if (!$this->validate($rules)) {
            $response = [
                "status" => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                "message" => $this->validator->getErrors(),
                "error" => true,
                "data" => []
            ];

            return $this->respond($response, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userObjet = new UserModel();
        $user = new User([
            "username" => $this->request->getVar("username"),
            "email" => $this->request->getVar("email"),
            "password" => $this->request->getVar("password")
        ]);

        // This won't save password directly to the users table
        // The password will be hashed and stored in auth_identities table
        // with the types is 'email_password' 
        $userObjet->save($user);

        $response = [
            "status" => ResponseInterface::HTTP_OK,
            "message" => "User created successfully",
            "error" => false,
            "data" => []
        ];
        return $this->respond($response, ResponseInterface::HTTP_OK);
    }

    /**
     * Login the user
     *
     * @return void
     */
    public function login()
    {
        // Handle user login and also generate token
        if (auth()->loggedIn()) {
            auth()->logout();
        }

        $rules = [
            "email" => "required|valid_email",
            "password" => "required"
        ];

        if (!$this->validate($rules)) {
            $response = [
                "status" => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                "message" => $this->validator->getErrors(),
                "error" => true,
                "data" => []
            ];

            return $this->respond($response, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $credentials = [
            "email" => $this->request->getVar("email"),
            "password" => $this->request->getVar("password")
        ];

        $loginAttempt = auth()->attempt($credentials);

        if (!$loginAttempt->isOK()) {
            $response = [
                "status" => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                "message" => "Invalid credentials",
                "error" => true,
                "data" => []
            ];

            return $this->respond($response, ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }

        $userObject = new UserModel();
        $user_data = $userObject->findById(auth()->id());
        // This token will also saved inside auth_identities tables
        // but with token name types, so that when user logged out
        // it will find the token name and revoked every single token
        $token = $user_data->generateAccessToken(self::TOKEN_NAME);
        $auth_token = $token->raw_token;

        $response = [
            "status" => ResponseInterface::HTTP_OK,
            "message" => "User logged in",
            "error" => false,
            "data" => [
                "token" => $auth_token,
            ]
        ];

        return $this->respond($response, ResponseInterface::HTTP_OK);
    }

    /**
     * Get current logged in user's profile
     *
     * @return void
     */
    public function profile()
    {
        // Get logged is user info
        if (auth("tokens")->loggedIn()) {
            $userId = auth()->id();
            $userObject = new UserModel();
            $userData = $userObject->findById($userId);

            return $this->respond($this->genericResponse(
                ResponseInterface::HTTP_OK,
                "User profile",
                false,
                [
                    "user" => $userData
                ]
            ), ResponseInterface::HTTP_OK);
        }
    }

    /**
     * Logout the current logged in user
     *
     * @return void
     */
    public function logout()
    {
        // Handle user logout, destroy token
        if (auth()->loggedIn()) {
            auth()->logout();
            auth()->user()->revokeAllAccessTokens();

            return $this->respond($this->genericResponse(
                ResponseInterface::HTTP_OK,
                "User logged out successfully",
                false,
                []
            ), ResponseInterface::HTTP_OK);
        }
    }

    /**
     * Handle for request that require the user to be logged in
     *
     * @return void
     */
    public function invalidRequest()
    {
        return $this->respond($this->genericResponse(
            ResponseInterface::HTTP_FORBIDDEN,
            "Invalid Request, please login",
            true,
            []
        ), ResponseInterface::HTTP_FORBIDDEN);
    }

    /**
     * Custom response
     *
     * @param  int $status
     * @param  string|array $message
     * @param  bool $error
     * @param  array $data
     * @return array
     */
    public function genericResponse(int $status, string | array $message, bool $error, array $data): array
    {
        return [
            "status" => $status,
            "message" => $message,
            "error" => $error,
            "data" => $data,
        ];
    }
}
