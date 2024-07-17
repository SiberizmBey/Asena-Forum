<?php
session_start();
require 'db.php';
require 'vendor/autoload.php';

use Webauthn\PublicKeyCredentialLoader;
use Webauthn\Server;
use Webauthn\PublicKeyCredentialSourceRepository;
use Webauthn\PublicKeyCredentialSource;

$user_id = $_SESSION['user_id'];

$publicKeyCredentialLoader = new PublicKeyCredentialLoader();
$publicKeyCredential = $publicKeyCredentialLoader->loadArray($_POST);

$rpEntity = new PublicKeyCredentialRpEntity('example.com', 'Example App');
$server = new Server($rpEntity, new PublicKeyCredentialSourceRepository());

$server->loadAndCheckAttestationResponse($publicKeyCredential, $_SESSION['webauthn_creation_options']);

$credentialSource = PublicKeyCredentialSource::createFromPublicKeyCredential($publicKeyCredential, $user_id);

// Kaydedin
$sql = "INSERT INTO webauthn_credentials (user_id, credential_id, public_key, counter) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('issi', $user_id, $credentialSource->getPublicKeyCredentialId(), $credentialSource->getCredentialPublicKey(), $credentialSource->getCounter());
$stmt->execute();

header('Location: profile.php');
exit();
