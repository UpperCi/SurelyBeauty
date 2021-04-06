<?php
# kijk of array geldige login-data bevat
function checkLogin(PDO $conn, $arr)
{
    if (isset($arr['user']) && isset($arr['pass'])) {
        $statement = $conn->prepare("SELECT * FROM admin_accounts WHERE email=:user");
        $statement->execute([':user' => $arr['user']]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($result != FALSE) {
            $pass = $result[0]['pass'];
            return password_verify($arr['pass'], $pass);
        }
    }
    return false;
}

# kijk of gebruiker ingelogd is en of dit ingelogde account add_accounts aan heeft staan
function checkAdmin($conn, $arr)
{
    if (isset($arr['user']) && isset($arr['pass'])) {
        $statement = $conn->prepare("SELECT * FROM admin_accounts WHERE email=:user");
        $statement->execute([':user' => $arr['user']]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if ($result != FALSE) {
            $pass = $result[0]['pass'];
            if (password_verify($arr['pass'], $pass)) {
                return boolval(intval($result[0]['add_accounts']) == 1);
            }
        }
    }
    return false;
}

# voeg requireLogin($connection) toe aan de bovenkant van de pagina en that's it
function requireLogin($conn)
{
    session_start();
    if (!checkLogin($conn, $_SESSION)) {
        if (checkLogin($conn, $_POST)) {
            $_SESSION['user'] = $_POST['user'];
            $_SESSION['pass'] = $_POST['pass'];
        } else header("Location: login.php");
    }
}
