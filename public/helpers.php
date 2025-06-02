<?php

const VITE_HOST = 'http://localhost:5173';

function vite(string $entry): string
{
  $viteHost = VITE_HOST;

  if (isDev($entry)) {
    return <<<HTML
      <script type="module">
        import RefreshRuntime from "{$viteHost}/@react-refresh"
        RefreshRuntime.injectIntoGlobalHook(window)
        window.\$RefreshReg$ = () => {}
        window.\$RefreshSig$ = () => (type) => type
        window.__vite_plugin_react_preamble_installed__ = true
      </script>
      <script type="module" src="{$viteHost}/@vite/client"></script>
      <script defer type="module" src="{$viteHost}/{$entry}"></script>
    HTML;
  }

  return "\n" . jsTag($entry)
    . "\n" . jsPreloadImports($entry)
    . "\n" . cssTag($entry);
}

function isDev(string $entry): bool
{
  static $exists = null;

  if ($exists !== null) {
    return $exists;
  }

  $handle = curl_init('http://vite:5173' . '/' . $entry);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_NOBODY, true);

  curl_exec($handle);
  $error = curl_errno($handle);
  curl_close($handle);

  return $exists = !$error;
}

function jsTag(string $entry): string
{
  $url = isDev($entry)
    ? VITE_HOST . '/' . $entry
    : assetUrl($entry);

  if (!$url) {
    return '';
  }

  if (isDev($entry)) {
    return '<script type="module" src="' . VITE_HOST . '/@vite/client"></script>' . "\n"
      . '<script type="module" src="' . $url . '"></script>';
  }

  return '<script type="module" src="' . $url . '"></script>';
}

function jsPreloadImports(string $entry): string
{
  if (isDev($entry)) {
    return '';
  }

  $res = '';
  foreach (importsUrls($entry) as $url) {
    $res .= '<link rel="modulepreload" href="' . $url . '">';
  }

  return $res;
}

function cssTag(string $entry): string
{
  if (isDev($entry)) {
    return '';
  }

  $tags = '';
  foreach (cssUrls($entry) as $url) {
    $tags .= '<link rel="stylesheet" href="' . $url . '">';
  }

  return $tags;
}

function getManifest(): array
{
  $content = file_get_contents(__DIR__ . '/dist/.vite/manifest.json');

  return json_decode($content, true);
}

function assetUrl(string $entry): string
{
  $manifest = getManifest();

  return isset($manifest[$entry])
    ? '/dist/' . $manifest[$entry]['file']
    : '';
}

function importsUrls(string $entry): array
{
  $urls = [];
  $manifest = getManifest();

  if (!empty($manifest[$entry]['imports'])) {
    foreach ($manifest[$entry]['imports'] as $imports) {
      $urls[] = '/dist/' . $manifest[$imports]['file'];
    }
  }

  return $urls;
}

function cssUrls(string $entry): array
{
  $urls = [];
  $manifest = getManifest();

  if (!empty($manifest[$entry]['css'])) {
    foreach ($manifest[$entry]['css'] as $file) {
      $urls[] = '/dist/' . $file;
    }
  }

  return $urls;
}
