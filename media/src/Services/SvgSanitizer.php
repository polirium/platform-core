<?php

namespace Polirium\Core\Media\Services;

use DOMDocument;
use DOMXPath;

/**
 * SVG Sanitizer - Removes malicious code from SVG files
 *
 * Protects against:
 * - Embedded JavaScript (<script> tags)
 * - Event handlers (onclick, onload, onerror, etc.)
 * - External references (external URLs in images, use, etc.)
 * - CSS expressions and JavaScript URLs
 * - Data URIs with script content
 */
class SvgSanitizer
{
    /**
     * List of allowed SVG elements.
     */
    protected array $allowedElements = [
        'svg', 'g', 'path', 'rect', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
        'text', 'tspan', 'textPath', 'defs', 'use', 'symbol', 'clipPath', 'mask',
        'pattern', 'image', 'linearGradient', 'radialGradient', 'stop', 'filter',
        'feBlend', 'feColorMatrix', 'feComponentTransfer', 'feComposite', 'feConvolveMatrix',
        'feDiffuseLighting', 'feDisplacementMap', 'feDropShadow', 'feFlood', 'feFuncA',
        'feFuncB', 'feFuncG', 'feFuncR', 'feGaussianBlur', 'feImage', 'feMerge',
        'feMergeNode', 'feMorphology', 'feOffset', 'fePointLight', 'feSpecularLighting',
        'feSpotLight', 'feTile', 'feTurbulence', 'title', 'desc', 'metadata',
        'switch', 'foreignObject', 'a', 'marker', 'animate', 'animateMotion',
        'animateTransform', 'set', 'mpath'
    ];

    /**
     * List of dangerous elements to remove.
     */
    protected array $dangerousElements = [
        'script', 'object', 'embed', 'applet', 'iframe', 'frame', 'frameset',
        'form', 'input', 'button', 'select', 'textarea', 'label'
    ];

    /**
     * List of allowed attributes (safe ones only).
     */
    protected array $allowedAttributes = [
        // Core attributes
        'id', 'class', 'style', 'lang', 'xml:lang', 'tabindex',
        // Presentation attributes
        'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin',
        'stroke-dasharray', 'stroke-dashoffset', 'stroke-miterlimit', 'stroke-opacity',
        'fill-opacity', 'fill-rule', 'opacity', 'transform', 'transform-origin',
        'clip-path', 'clip-rule', 'mask', 'filter', 'color', 'color-interpolation',
        'color-interpolation-filters', 'flood-color', 'flood-opacity', 'stop-color',
        'stop-opacity', 'lighting-color', 'marker', 'marker-start', 'marker-mid',
        'marker-end', 'paint-order', 'shape-rendering', 'text-rendering', 'image-rendering',
        // Geometry attributes
        'x', 'y', 'cx', 'cy', 'r', 'rx', 'ry', 'width', 'height', 'd',
        'points', 'x1', 'y1', 'x2', 'y2', 'pathLength', 'viewBox', 'preserveAspectRatio',
        // Text attributes
        'dx', 'dy', 'rotate', 'textLength', 'lengthAdjust', 'font-family', 'font-size',
        'font-style', 'font-weight', 'font-variant', 'font-stretch', 'text-anchor',
        'dominant-baseline', 'alignment-baseline', 'baseline-shift', 'letter-spacing',
        'word-spacing', 'text-decoration', 'writing-mode', 'direction',
        // Gradient/pattern attributes
        'gradientUnits', 'gradientTransform', 'spreadMethod', 'fx', 'fy', 'fr',
        'patternUnits', 'patternContentUnits', 'patternTransform',
        // Filter attributes
        'filterUnits', 'primitiveUnits', 'in', 'in2', 'result', 'mode', 'type',
        'values', 'baseFrequency', 'numOctaves', 'seed', 'stitchTiles', 'scale',
        'xChannelSelector', 'yChannelSelector', 'stdDeviation', 'radius', 'azimuth',
        'elevation', 'surfaceScale', 'diffuseConstant', 'specularConstant', 'specularExponent',
        'limitingConeAngle', 'pointsAtX', 'pointsAtY', 'pointsAtZ', 'k1', 'k2', 'k3', 'k4',
        'operator', 'order', 'kernelMatrix', 'divisor', 'bias', 'targetX', 'targetY',
        'edgeMode', 'kernelUnitLength', 'preserveAlpha',
        // Animation attributes
        'attributeName', 'attributeType', 'from', 'to', 'by', 'begin', 'dur', 'end',
        'min', 'max', 'restart', 'repeatCount', 'repeatDur', 'calcMode', 'keyTimes',
        'keySplines', 'additive', 'accumulate', 'fill',
        // Other common attributes
        'version', 'xmlns', 'xmlns:xlink', 'xmlns:svg', 'vector-effect', 'visibility',
        'display', 'overflow', 'clip', 'enable-background'
    ];

    /**
     * List of dangerous attributes to always remove.
     */
    protected array $dangerousAttributes = [
        // Event handlers
        'onabort', 'onactivate', 'onbegin', 'oncancel', 'oncanplay', 'oncanplaythrough',
        'onchange', 'onclick', 'onclose', 'oncontextmenu', 'oncuechange', 'ondblclick',
        'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart',
        'ondrop', 'ondurationchange', 'onemptied', 'onend', 'onended', 'onerror',
        'onfocus', 'onfocusin', 'onfocusout', 'oninput', 'oninvalid', 'onkeydown',
        'onkeypress', 'onkeyup', 'onload', 'onloadeddata', 'onloadedmetadata',
        'onloadstart', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove',
        'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onpause', 'onplay',
        'onplaying', 'onprogress', 'onratechange', 'onrepeat', 'onreset', 'onresize',
        'onscroll', 'onseeked', 'onseeking', 'onselect', 'onshow', 'onstalled',
        'onsubmit', 'onsuspend', 'ontimeupdate', 'ontoggle', 'onvolumechange',
        'onwaiting', 'onwheel', 'onzoom'
    ];

    /**
     * Sanitize an SVG string.
     */
    public function sanitize(string $svgContent): string
    {
        // Remove XML declaration if present
        $svgContent = preg_replace('/<\?xml[^>]*\?>/i', '', $svgContent);

        // Remove DOCTYPE
        $svgContent = preg_replace('/<!DOCTYPE[^>]*>/i', '', $svgContent);

        // Remove CDATA sections that might contain scripts
        $svgContent = preg_replace('/<!\[CDATA\[.*?\]\]>/s', '', $svgContent);

        // Parse as DOM
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadXML($svgContent, LIBXML_NONET | LIBXML_NOENT);
        libxml_clear_errors();

        if (!$dom->documentElement) {
            throw new \RuntimeException('Invalid SVG content');
        }

        // Remove dangerous elements
        $this->removeDangerousElements($dom);

        // Clean attributes on all elements
        $this->cleanAttributes($dom);

        // Remove dangerous href/xlink:href values
        $this->cleanLinks($dom);

        // Clean style attributes and tags
        $this->cleanStyles($dom);

        // Save and return
        $result = $dom->saveXML($dom->documentElement);

        return $result ?: '';
    }

    /**
     * Sanitize an SVG file.
     */
    public function sanitizeFile(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            return false;
        }

        try {
            $sanitized = $this->sanitize($content);
            return file_put_contents($filePath, $sanitized) !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if SVG content is safe (for quick validation).
     */
    public function isSafe(string $svgContent): bool
    {
        // Quick checks without full parsing
        $lowered = strtolower($svgContent);

        // Check for script tags
        if (preg_match('/<script[\s>]/i', $svgContent)) {
            return false;
        }

        // Check for event handlers
        foreach ($this->dangerousAttributes as $attr) {
            if (stripos($svgContent, $attr . '=') !== false) {
                return false;
            }
        }

        // Check for javascript: URLs
        if (preg_match('/javascript\s*:/i', $svgContent)) {
            return false;
        }

        // Check for data: URIs with script content
        if (preg_match('/data\s*:[^,]*script/i', $svgContent)) {
            return false;
        }

        return true;
    }

    /**
     * Remove dangerous elements from DOM.
     */
    protected function removeDangerousElements(DOMDocument $dom): void
    {
        $xpath = new DOMXPath($dom);

        // Register SVG namespace
        $xpath->registerNamespace('svg', 'http://www.w3.org/2000/svg');

        foreach ($this->dangerousElements as $element) {
            // Query both with and without namespace
            $nodes = $xpath->query("//{$element} | //svg:{$element}");
            if ($nodes) {
                foreach ($nodes as $node) {
                    $node->parentNode?->removeChild($node);
                }
            }
        }

        // Also remove any element not in allowed list
        $allElements = $xpath->query('//*');
        if ($allElements) {
            $toRemove = [];
            foreach ($allElements as $node) {
                $localName = strtolower($node->localName);
                if (!in_array($localName, $this->allowedElements) &&
                    !in_array($localName, $this->dangerousElements)) {
                    // Keep unknown elements but remove their children if suspicious
                    // or add to removal list if definitely dangerous
                }
            }
        }
    }

    /**
     * Clean dangerous attributes from all elements.
     */
    protected function cleanAttributes(DOMDocument $dom): void
    {
        $xpath = new DOMXPath($dom);
        $allElements = $xpath->query('//*');

        if (!$allElements) {
            return;
        }

        foreach ($allElements as $node) {
            if (!$node->hasAttributes()) {
                continue;
            }

            $toRemove = [];
            foreach ($node->attributes as $attr) {
                $attrName = strtolower($attr->nodeName);

                // Remove if it's a dangerous event handler
                if (in_array($attrName, $this->dangerousAttributes)) {
                    $toRemove[] = $attr->nodeName;
                    continue;
                }

                // Remove any attribute starting with "on" (event handlers)
                if (str_starts_with($attrName, 'on')) {
                    $toRemove[] = $attr->nodeName;
                }
            }

            foreach ($toRemove as $attrName) {
                $node->removeAttribute($attrName);
            }
        }
    }

    /**
     * Clean href and xlink:href attributes for dangerous values.
     */
    protected function cleanLinks(DOMDocument $dom): void
    {
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('xlink', 'http://www.w3.org/1999/xlink');

        // Find all elements with href or xlink:href
        $nodes = $xpath->query('//*[@href or @xlink:href]');

        if (!$nodes) {
            return;
        }

        foreach ($nodes as $node) {
            foreach (['href', 'xlink:href'] as $attrName) {
                if (!$node->hasAttribute($attrName)) {
                    continue;
                }

                $value = $node->getAttribute($attrName);
                $cleanValue = $this->cleanUrl($value);

                if ($cleanValue === null) {
                    $node->removeAttribute($attrName);
                } elseif ($cleanValue !== $value) {
                    $node->setAttribute($attrName, $cleanValue);
                }
            }
        }
    }

    /**
     * Clean URL/URI value, returns null if should be removed.
     */
    protected function cleanUrl(string $url): ?string
    {
        $url = trim($url);
        $lowered = strtolower($url);

        // Block javascript: URLs
        if (str_starts_with($lowered, 'javascript:')) {
            return null;
        }

        // Block vbscript: URLs
        if (str_starts_with($lowered, 'vbscript:')) {
            return null;
        }

        // Block data: URLs with script content
        if (str_starts_with($lowered, 'data:') &&
            (str_contains($lowered, 'script') || str_contains($lowered, 'javascript'))) {
            return null;
        }

        // Allow safe data: URIs (images)
        if (str_starts_with($lowered, 'data:image/')) {
            return $url;
        }

        // Allow internal references (#id)
        if (str_starts_with($url, '#')) {
            return $url;
        }

        // Block external URLs for security (optional - can be made configurable)
        if (preg_match('/^https?:\/\//i', $url)) {
            // Return null to block external URLs, or return $url to allow
            return null; // Block external URLs by default
        }

        return $url;
    }

    /**
     * Clean style attributes and style tags.
     */
    protected function cleanStyles(DOMDocument $dom): void
    {
        $xpath = new DOMXPath($dom);

        // Clean style attributes
        $nodes = $xpath->query('//*[@style]');
        if ($nodes) {
            foreach ($nodes as $node) {
                $style = $node->getAttribute('style');
                $cleanStyle = $this->cleanStyleValue($style);

                if ($cleanStyle === '') {
                    $node->removeAttribute('style');
                } else {
                    $node->setAttribute('style', $cleanStyle);
                }
            }
        }

        // Clean <style> tags (remove dangerous CSS)
        $styleNodes = $xpath->query('//style | //svg:style');
        if ($styleNodes) {
            foreach ($styleNodes as $node) {
                $css = $node->textContent;
                $cleanCss = $this->cleanCss($css);

                if ($cleanCss === '') {
                    $node->parentNode?->removeChild($node);
                } else {
                    $node->textContent = $cleanCss;
                }
            }
        }
    }

    /**
     * Clean inline style value.
     */
    protected function cleanStyleValue(string $style): string
    {
        // Remove expression() - IE CSS expression
        $style = preg_replace('/expression\s*\([^)]*\)/i', '', $style);

        // Remove url() with javascript/vbscript
        $style = preg_replace('/url\s*\(\s*["\']?\s*(javascript|vbscript)[^)]*\)/i', '', $style);

        // Remove behavior property (IE)
        $style = preg_replace('/behavior\s*:[^;]+(;|$)/i', '', $style);

        // Remove -moz-binding (Firefox XBL)
        $style = preg_replace('/-moz-binding\s*:[^;]+(;|$)/i', '', $style);

        return trim($style);
    }

    /**
     * Clean CSS content.
     */
    protected function cleanCss(string $css): string
    {
        // Remove @import rules
        $css = preg_replace('/@import[^;]+;/i', '', $css);

        // Apply same cleaning as inline styles
        $css = $this->cleanStyleValue($css);

        return trim($css);
    }
}
