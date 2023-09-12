<?php

namespace App\Auth\DataObjects;

use Psr\Http\Message\ResponseInterface;

class AccessToken
{
    public ?string $accessToken;
    public ?string $scope;
    public ?string $expiresIn;
    public ?string $type;
    public function __construct(private ResponseInterface $oauthResponse)
    {
        $json = $this->oauthResponse->getBody()->getContents();
        $data = json_decode($json);
        $this->accessToken = $data->access_token ?? null;
        $this->scope = $data->scope ?? null;
        $this->expiresIn = $data->expires_in ?? null;
        $this->type = $data->token_type ?? null;
    }
}
