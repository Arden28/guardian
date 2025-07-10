<?php

namespace Arden28\Guardian\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class TelegramProvider extends AbstractProvider
{
    /**
     * The base URL for Telegram authentication.
     *
     * @var string
     */
    protected $authUrl = 'https://telegram.org';

    /**
     * The scopes being requested.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * Get the authentication URL for the provider.
     *
     * @param string $state
     * @return string
     */
    protected function getAuthUrl($state)
    {
        // Telegram Login Widget is handled on the frontend; return redirect URI
        return $this->buildAuthUrlFromBase(config('guardian.socialite.drivers.telegram.redirect'));
    }

    /**
     * Get the token URL for the provider (not used for Telegram).
     *
     * @return string
     */
    protected function getTokenUrl()
    {
        return null; // Telegram uses bot token validation, not OAuth token
    }

    /**
     * Get the raw user for the given access token (not used for Telegram).
     *
     * @param string $token
     * @return array
     */
    protected function getUserByToken($token)
    {
        return []; // Not applicable for Telegram
    }

    /**
     * Map the raw user array to a Socialite User instance.
     *
     * @param array $user
     * @return User
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'name' => $user['first_name'] . ' ' . $user['last_name'] ?? null,
            'email' => null, // Telegram may not provide email
            'avatar' => $user['photo_url'] ?? null,
        ]);
    }

    /**
     * Validate Telegram Login Widget data.
     *
     * @param array $data
     * @return User
     * @throws \Exception
     */
    public function validateTelegramData(array $data)
    {
        $botToken = config('guardian.socialite.drivers.telegram.bot_token');

        // Verify Telegram data integrity
        $checkHash = $data['hash'];
        unset($data['hash']);
        ksort($data);
        $dataCheckString = '';
        foreach ($data as $key => $value) {
            $dataCheckString .= $key . '=' . $value . "\n";
        }
        $dataCheckString = rtrim($dataCheckString, "\n");
        $secretKey = hash('sha256', $botToken, true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (strcmp($hash, $checkHash) !== 0) {
            throw new \Exception('Invalid Telegram data');
        }

        // Check if data is not expired
        if (isset($data['auth_date']) && (time() - $data['auth_date']) > 86400) {
            throw new \Exception('Telegram data expired');
        }

        return $this->mapUserToObject($data);
    }
}