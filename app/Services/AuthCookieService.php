<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AccessToken;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Contracts\Encryption\StringEncrypter;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie as CookieFacade;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\JsonException;
use Safe\Exceptions\UrlException;
use Symfony\Component\HttpFoundation\Cookie;

use function array_slice;
use function base64_decode;
use function config;
use function explode;
use function implode;
use function request;
use function Safe\file_get_contents;
use function Safe\json_decode;
use function Safe\json_encode;
use function Safe\parse_url;
use function trim;

use const PHP_URL_HOST;

final class AuthCookieService
{
    private Cookie $cookie;
    private string $cookieName;
    private string $domain;
    private ?StringEncrypter $encrypter;

    public function __construct()
    {
        $this->cookieName = (string) config("nde.shared_cookie_name");

        $sharedKey = (string) config("nde.shared_key");
        $sharedKeyUrl = (string) config("nde.shared_key_url");

        if (empty($sharedKey) && empty($sharedKeyUrl)) {
            $this->encrypter = Crypt::getFacadeRoot();
        } else {
            $key = $this->getKey($sharedKey, $sharedKeyUrl);

            try {
                $this->encrypter = empty($key) ? null : new Encrypter($key, config("app.cipher"));
            } catch (RuntimeException $e) {
                Log::error($e->getMessage());

                $this->encrypter = null;
            }
        }
    }

    /**
     * @return array{
     *     access_token: string,
     *     expires: int,
     *     refresh_token: string,
     * }
     */
    private function decrypt(string $cipherText): array
    {
        try {
            $value = json_decode(
                $this->encrypter ? $this->encrypter->decryptString($cipherText) : $cipherText,
                true,
            );
        } catch (DecryptException|JsonException $e) {
            Log::debug($e->getMessage());

            $value = [];
        }

        return Arr::wrap($value);
    }

    private function encrypt(AccessToken $accessToken): string
    {
        try {
            $json = json_encode([
                "access_token" => $accessToken->getAccessToken(),
                "expires" => $accessToken->getExpiresAt(),
                "refresh_token" => $accessToken->getRefreshToken(),
            ]);
        } catch (JsonException $e) {
            Log::debug($e->getMessage());

            $json = "{}";
        }

        try {
            return $this->encrypter ? $this->encrypter->encryptString($json) : $json;
        } catch (EncryptException $e) {
            Log::debug($e->getMessage());

            return "";
        }
    }

    public function expireCookie(): void
    {
        $this->cookie = CookieFacade::forget(
            $this->cookieName,
            null,
            $this->getDomain(),
        );

        CookieFacade::queue($this->cookie);
    }

    public function get(): Cookie
    {
        if (empty($this->cookie)) {
            $value = CookieFacade::get($this->cookieName);

            if (empty($value)) {
                $this->expireCookie();
            } else {
                $this->setCookie((string) $value);
            }
        }

        return $this->cookie;
    }

    public function getAccessToken(): AccessToken
    {
        $value = $this->decrypt($this->get()->getValue() ?? "{}");

        return new AccessToken(
            (string) Arr::get($value, "access_token", ""),
            (string) Arr::get($value, "refresh_token", ""),
            (int) Arr::get($value, "expires", 0),
        );
    }

    private function getDomain(): string
    {
        if (empty($this->domain)) {
            $request = request();
            $url = $request ? $request->url() : "";

            try {
                $host = parse_url($url, PHP_URL_HOST) ?? "";
                $this->domain = implode(".", array_slice(explode(".", $host), -2));
            } catch (UrlException $e) {
                Log::debug($e->getMessage());

                $this->domain = "";
            }
        }

        return $this->domain;
    }

    private function getKey(string $sharedKey, string $sharedKeyUrl): string
    {
        if (!empty($sharedKey)) {
            if (Str::startsWith($sharedKey, $prefix = "base64:")) {
                $sharedKey = base64_decode(Str::after($sharedKey, $prefix), true) ?: "";
            }

            return $sharedKey;
        }

        try {
            return trim(file_get_contents($sharedKeyUrl));
        } catch (FilesystemException $e) {
            Log::debug($e->getMessage());

            return "";
        }
    }

    public function set(AccessToken $accessToken): void
    {
        $this->setCookie($this->encrypt($accessToken));
    }

    private function setCookie(string $cipherText): void
    {
        $this->cookie = CookieFacade::make(
            $this->cookieName,
            $cipherText,
            0,
            null,
            $this->getDomain(),
            (bool) config("session.secure"),
        );

        CookieFacade::queue($this->cookie);
    }
}
