<?php
// Write to a JSON file
// One line per file
require_once("../include/bittorrent.php");

require_once($WWW_ROOT . 'include/categtags/torrent_edit_inc.php');
require_once("./torrents_import_inc.php")

function updateLastId($domain, $lastId) {
  q('UPDATE torrents_importer_status SET last_id = :lastId WHERE domain = :host',
    array('lastId' => $lastId, 'host' => $domain)
  );
}

function getLastId($domain) {
    $lastId = fetchOne('
    SELECT last_id
    FROM torrents_importer_status
    WHERE domain = :domain', array('domain' => $domain)
  );

  if (!is_numeric($lastId)) {
    q('insert into torrents_importer_status (domain, last_id) VALUES (:domain, :last_id)',
      array('domain'  => $domain,'last_id' => 0)
    );
    $lastId = 0;
  }
}

echo nextIteration(0);

function nextIteration($processed_counter) {
  $globalImportTorrentsFromUrl  = 'https://'; // Update with the domain that has /tools/torrents_export.php script
  $fullImageBase                = $globalImportTorrentsFromUrl . '/torrents_img/';
  $parsedUrl                    = parse_url($globalImportTorrentsFromUrl);
  $globalImportTorrentsFromHost = $parsedUrl['host'];

  $lastId = getLastId($globalImportTorrentsFromHost);

  $url              = $globalImportTorrentsFromUrl . '/tools/torrents_export.php?start_id=' . $lastId;
  $receivedDataJson = file_get_contents($url);
  $receivedData     = json_decode($receivedDataJson, true);

  if (!isset($receivedData['torrents'])) {
    die('Bad format');
  }

  $torrents = $receivedData['torrents'];

  if (empty($torrents)) {
    die('No new torrents available');
  }

  foreach ($torrents as $torrent) {
    $remoteTorrentId = $torrent['id'];

    $result   = import_torrent($torrent, $fullImageBase);
    $inserted = $result['inserted'];

    updateLastId($globalImportTorrentsFromHost, $remoteTorrentId);

    if ($inserted == false) {
      $reason = $result['reason'];
      echo "$remoteTorrentId not inserted, reason $reason\n<br/>";
    }
  }

  echo "Processed " . $processed_counter . ", current remote id: $remoteTorrentId\n";

  event_torrent_changed_any();

  return nextIteration($processed_counter + 100);
}

