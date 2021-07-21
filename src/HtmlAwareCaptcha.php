<?php

declare(strict_types=1);

namespace Charcoal\ReCaptcha;

use function htmlspecialchars;
use function http_build_query;
use function implode;
use function is_array;
use function sprintf;

/**
 * HTML aware service wrapper for the Google reCAPTCHA client
 */
class HtmlAwareCaptcha extends AbstractCaptchaDecorator
{
    /**
     * The JavaScript API for Google reCAPTCHA.
     *
     * @const string
     */
    public const CLIENT_API = 'https://www.google.com/recaptcha/api.js';

    /**
     * Render the HTML script and widget.
     *
     * @param  array|bool $attributes  Optional. The HTML attributes for the 'g-recaptcha' tag.
     * @param  array|bool $queryParams Optional. The query parameters for the JavaScript API link.
     * @return string Returns a HTML `<script>` and `<div>` elements.
     */
    public function display($attributes = true, $queryParams = true): string
    {
        $html = '';

        if ($queryParams) {
            if (!is_array($queryParams)) {
                $queryParams = [];
            }

            $html .= $this->getJsHtml($queryParams);
        }

        if ($attributes) {
            if (!is_array($attributes)) {
                $attributes = [];
            }

            if ($html) {
                $html .= "\n";
            }

            $html .= $this->getWidgetHtml($attributes);
        }

        return $html;
    }

    /**
     * Render the HTML widget.
     *
     * @link   https://developers.google.com/recaptcha/docs/display
     * @param  array $attributes Optional. The HTML attributes for the 'g-recaptcha' tag.
     * @return string Returns a HTML `<div>` element.
     */
    public function getWidgetHtml(array $attributes = []): string
    {
        $attributes['data-sitekey'] = $this->getConfig('public_key');

        return '<div class="g-recaptcha"' . $this->buildAttributes($attributes) . '></div>';
    }

    /**
     * Create the HTML `<script>` element to load the JavaScript API.
     *
     * @param  array|null $query Optional. Array of query string arguments to customize the API.
     * @return string Returns a HTML `<script>` element.
     */
    public function getJsHtml(array $query = null): string
    {
        return sprintf(
            '<script src="%s" async defer></script>',
            $this->getJsUri($query)
        );
    }

    /**
     * Create the URI to the JavaScript API with parameters.
     *
     * @link   https://developers.google.com/recaptcha/docs/display
     * @param  array $query Optional. Array of query string arguments to customize the API.
     * @return string Returns a URI.
     */
    public function getJsUri(array $query = null): string
    {
        if (isset($query['lang'])) {
            $query['hl'] = $query['lang'];
            unset($query['lang']);
        }

        if ($query) {
            return static::CLIENT_API . '?' . http_build_query($query);
        } else {
            return static::CLIENT_API;
        }
    }

    /**
     * Build HTML attributes.
     *
     * @param  array $attributes Associative array of attribute names and values.
     * @return string Returns a string of HTML attributes.
     */
    protected function buildAttributes(array $attributes): string
    {
        $html = [];
        foreach ($attributes as $key => $value) {
            $html[] = $key . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
        }

        return $html ? ' ' . implode(' ', $html) : '';
    }
}
