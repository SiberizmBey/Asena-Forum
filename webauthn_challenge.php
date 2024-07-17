<?php
session_start();
require 'db.php';
require 'vendor/autoload.php';

use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\PublicKeyCredentialParameters;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\AuthenticatorAttachment;

$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini alın
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$rpEntity = new PublicKeyCredentialRpEntity('example.com', 'Example App');
$userEntity = new PublicKeyCredentialUserEntity($user['username'], $user['id'], $user['username']);
$pubKeyCredParams = [
    new PublicKeyCredentialParameters('public-key', -7), // ES256
    new PublicKeyCredentialParameters('public-key', -257), // RS256
];
$authenticatorSelection = new AuthenticatorSelectionCriteria();
$authenticatorSelection->setAuthenticatorAttachment(AuthenticatorAttachment::CROSS_PLATFORM);

$creationOptions = new PublicKeyCredentialCreationOptions(
    $rpEntity,
    $userEntity,
    random_bytes(32), // Challenge
    $pubKeyCredParams,
    $authenticatorSelection
);

$_SESSION['webauthn_creation_options'] = $creationOptions;

header('Content-Type: application/json');
echo json_encode($creationOptions);
