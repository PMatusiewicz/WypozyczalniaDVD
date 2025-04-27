<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "wypozyczalnia"); 
mysqli_set_charset($connect, "utf8mb4");

if (!$connect) {
    die("Błąd połączenia z bazą danych: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Wypożyczalnia płyt DVD</title>
        <link rel="stylesheet" href="style.css">
        <link rel="shortcut icon" href="plyta.png" type="image/x-icon">
    </head>
        <header>
            <div class="zawartosc_headera">
                <img src="plyta.png" alt="plyta dvd" width="50px" style="float: left">
                <h1>Wypożyczalnia płyt DVD</h1>
            </div>
        </header>
        <main>
            <div class="formularze">
                <?php
                //wyswietla formularze logowania i rejestracji jezeli uzytkownik nie jest zalogowany
                if (empty($_SESSION['uzytkownik']))
                {
                    echo "<form action='index.php' method='post' class='formularz_logowania'>
                        <h3>Zarejestruj się</h3>
                        <label>
                            Nazwa użytkownika: <br>
                            <input type='text' name='rejestracja_nazwa' id='rejestracja_nazwa'>
                        </label>
                        <label>
                            Hasło: <br>
                            <input type='password' name='rejestracja_haslo' id='rejestracja_haslo'>
                        </label>
                        <button type='submit' name='rejestracja'>Zarejestruj</button>
                    </form>
                    <form action='index.php' method='post' class='formularz_logowania'>
                        <h3>Zaloguj się</h3>
                        <label>
                            Nazwa użytkownika: <br>
                            <input type='text' name='login_nazwa' id='login_nazwa'>
                        </label>
                        <label>
                            Hasło: <br>
                            <input type='password' name='login_haslo' id='login_haslo'>
                        </label>
                        <button type='submit' name='login'>Zaloguj</button>
                    </form>";
                }
                //a jak uzytkownik jest zalogowany wyswietla liste wypozyczonych plyt
                else
                {
                    //przycisk wylogowania
                    echo "<div class='lista_wypozyczonych'>
                    <form method='post'>
                        <button type='submit' name='wyloguj'>Wyloguj</button>
                    </form>";
                    if (isset($_POST['wyloguj'])) 
                    {
                        $_SESSION = [];
                        session_destroy();
                        header('Location: index.php');
                        exit();
                    }
                    //lista wypozyczonych plyt
                    echo "<p>Witaj " . htmlspecialchars($_SESSION['uzytkownik']) . ", twoje wypożyczone płyty:</p><ul>";
                    $stmt = mysqli_prepare($connect, "SELECT katalog.nazwa, katalog.rezyseria FROM katalog INNER JOIN wypozyczenia ON katalog.id = wypozyczenia.id_katalog INNER JOIN uzytkownicy ON uzytkownicy.id = wypozyczenia.id_uzytkownicy WHERE uzytkownicy.id = ? ORDER BY katalog.id");
                    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id_uzytkownika']);
                    mysqli_stmt_execute($stmt);
                    $wypozyczone_plyty = mysqli_stmt_get_result($stmt);
                    while ($row_wypozyczone = mysqli_fetch_assoc($wypozyczone_plyty))
                    {
                        echo "<li>" . htmlspecialchars($row_wypozyczone['nazwa']) . ", " . htmlspecialchars($row_wypozyczone['rezyseria']) . "</li>";
                    }
                    echo "</ul></div>";
                    
                }
                
                //rejestracja
                if (isset($_POST["rejestracja"]) && !empty($_POST["rejestracja_nazwa"]) && !empty($_POST["rejestracja_haslo"])) 
                {
                    $rejestracja_nazwa = trim($_POST["rejestracja_nazwa"]);
                    $rejestracja_haslo = trim($_POST["rejestracja_haslo"]);

                    //sprzawdzanie czy dlugosci nazwy i hasla sa poprawne
                    if (strlen($rejestracja_nazwa) < 3 || strlen($rejestracja_haslo) < 3 || strlen($rejestracja_nazwa) > 20 || strlen($rejestracja_haslo) > 20) 
                    {
                        echo "<p style='color:red;'>Nazwa i hasło muszą mieć od 3 do 20 znaków.</p>";
                    } 
                    else 
                    {
                        //sprawdzanie czy taki uzytkownik juz istnieje w bazie danych
                        $stmt = mysqli_prepare($connect, "SELECT id FROM uzytkownicy WHERE nazwa = ?");
                        mysqli_stmt_bind_param($stmt, "s", $rejestracja_nazwa);
                        mysqli_stmt_execute($stmt);
                        $istnieje = mysqli_stmt_get_result($stmt);
                        if (mysqli_num_rows($istnieje) > 0) 
                        {
                            echo "<p style='color:red;'>Taki użytkownik już istnieje. Wybierz inną nazwę.</p>";
                        }
                        else 
                        {
                            //dodawanie uzytkownika do bazy danych
                            $hashed_password = password_hash($rejestracja_haslo, PASSWORD_DEFAULT);
                            $stmt = mysqli_prepare($connect, "INSERT INTO uzytkownicy (nazwa, haslo) VALUES (?, ?)");
                            mysqli_stmt_bind_param($stmt, "ss", $rejestracja_nazwa, $hashed_password);
                            mysqli_stmt_execute($stmt);

                            $stmt = mysqli_prepare($connect, "SELECT id FROM uzytkownicy WHERE nazwa = ?");
                            mysqli_stmt_bind_param($stmt, "s", $rejestracja_nazwa);
                            mysqli_stmt_execute($stmt);
                            $nowy_uzytkownik = mysqli_stmt_get_result($stmt);
                            if ($wiersz = mysqli_fetch_assoc($nowy_uzytkownik)) 
                            {
                                //zapisuje zmienne do sesji i odswieza strone by widziec liste filmow zamiast formularze
                                $_SESSION['id_uzytkownika'] = $wiersz['id'];
                                $_SESSION['uzytkownik'] = $rejestracja_nazwa;
                                header("Location: index.php");
                                exit();
                            }
                        }
                    }
                }

                //logowanie
                if (isset($_POST["login"]) && !empty($_POST["login_nazwa"]) && !empty($_POST["login_haslo"]))
                {
                    $login_nazwa = trim($_POST["login_nazwa"]);
                    $login_haslo = trim($_POST["login_haslo"]);
                    
                    //sprawdzanie czy login i haslo sa poprawne
                    $stmt = mysqli_prepare($connect, "SELECT id, haslo FROM uzytkownicy WHERE nazwa = ?");
                    mysqli_stmt_bind_param($stmt, "s", $login_nazwa);
                    mysqli_stmt_execute($stmt);
                    $proba_logowania = mysqli_stmt_get_result($stmt);
                    if ($row = mysqli_fetch_assoc($proba_logowania)) 
                    {
                        if (password_verify($login_haslo, $row['haslo']))
                        {
                            //zapisuje zmienne do sesji i odswieza strone by widziec liste filmow zamiast formularze
                            $_SESSION['id_uzytkownika'] = $row['id'];
                            $_SESSION['uzytkownik'] = $login_nazwa;
                            header("Location: index.php");
                            exit();
                        }
                        else 
                        {
                            echo "<p style='color:red;'>Nieprawidłowa nazwa użytkownika lub hasło.</p>";
                        }
                    } 
                    else 
                    {
                        echo "<p style='color:red;'>Nieprawidłowa nazwa użytkownika lub hasło.</p>";
                    }
                }

                //ustawia jak sortowac filmy w katalogu w zaleznosci od kliknietej kategorii
                $kolumna_sortowania = "nazwa";
                $kolejnosc_sortowania = "ASC";

                if (isset($_GET['sortowanie'])) {
                    $dozwolone_kolumny = ['nazwa', 'rezyseria', 'gatunek', 'ilosc'];
                    if (in_array($_GET['sortowanie'], $dozwolone_kolumny)) {
                        $kolumna_sortowania = $_GET['sortowanie'];
                    }
                }
                if (isset($_GET['kolejnosc']) && ($_GET['kolejnosc'] == 'ASC' || $_GET['kolejnosc'] == 'DESC')) {
                    $kolejnosc_sortowania = $_GET['kolejnosc'];
                }
                ?>
            </div>
        </main>
        <section>
            <h2>Katalog płyt DVD</h2>
            <div class="tabela">
                <table>
                <tr>
                    <th><?= buduj_link_sortowania('nazwa', $kolumna_sortowania, $kolejnosc_sortowania) ?></th>
                    <th><?= buduj_link_sortowania('rezyseria', $kolumna_sortowania, $kolejnosc_sortowania) ?></th>
                    <th><?= buduj_link_sortowania('gatunek', $kolumna_sortowania, $kolejnosc_sortowania) ?></th>
                    <th><?= buduj_link_sortowania('ilosc', $kolumna_sortowania, $kolejnosc_sortowania) ?></th>
                    <th>Wypożyczanie</th>
                    <th>Zwracanie</th>
                </tr>
                    <?php
                    //funkcja ktora dodaje linki do naglowkow tabeli dzieki czemu mozna na nie kliknac i klikniecie zmienia sortowanie w tabeli w zaleznosci od kliknietego naglowka
                    function buduj_link_sortowania($kolumna, $aktualna_kolumna, $aktualna_kolejnosc) {
                        $nowa_kolejnosc = ($aktualna_kolumna == $kolumna && $aktualna_kolejnosc == 'ASC') ? 'DESC' : 'ASC';
                        return "<a href='?sortowanie=$kolumna&kolejnosc=$nowa_kolejnosc'>" . ucfirst($kolumna) . "</a>";
                    }

                    //klikniecie przyciskow wypozycz i zwroc
                    if (isset($_POST["action"]))
                    {
                        $action = $_POST["action"];
                        $id = intval($_POST["id"]);
                        //jezeli klikniety zostal przycisk wypozycz to usuwa ilosc wybranych plyt o 1 i dodaje plyte do wypozyczonych plyt zalogowanego uzytkownika
                        if ($action == "wypozycz")
                        {
                            mysqli_query($connect,"UPDATE katalog SET ilosc = ilosc - 1 WHERE id = $id");
                            mysqli_query($connect, "INSERT INTO wypozyczenia (id_katalog, id_uzytkownicy) VALUES ($id, {$_SESSION['id_uzytkownika']})");
                        }
                        //jezeli klikniety zostal przycisk zwroc to dodaje ilosc wybranych plyt o 1 i usuwa plyte z wypozyczonych plyt zalogowanego uzytkownika
                        elseif ($action == "zwroc")
                        {
                            mysqli_query($connect,"UPDATE katalog SET ilosc = ilosc + 1 WHERE id = $id");
                            mysqli_query($connect, "DELETE FROM wypozyczenia WHERE id_katalog = $id AND id_uzytkownicy = {$_SESSION['id_uzytkownika']}");
                        }
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit();
                    }
                    
                    //wypisywanie wszystkich plyt z bazy danych do tabeli
                    $plyty = mysqli_query($connect,"SELECT * FROM katalog ORDER BY $kolumna_sortowania $kolejnosc_sortowania");
                    while ($row = mysqli_fetch_assoc($plyty))
                    {
                        echo "<tr>
                        <td>" . htmlspecialchars($row['nazwa']) . "</td>
                        <td>" . htmlspecialchars($row['rezyseria']) . "</td>
                        <td>" . htmlspecialchars($row['gatunek']) . "</td>
                        <td>{$row['ilosc']}</td>
                        <td>";
                        //sprawdza czy jezeli uzytkownik jest zalogowany to czy juz wypozyczyl film
                        if (!empty($_SESSION['uzytkownik'])) 
                        {
                            $czy_uzytkownik_posiada = mysqli_query($connect,"SELECT * FROM wypozyczenia WHERE id_katalog = {$row['id']} AND id_uzytkownicy = {$_SESSION['id_uzytkownika']}");
                        } 
                        else 
                        {
                            $czy_uzytkownik_posiada = false;
                        }
                        //jezeli uzytkownik nie wypozyczyl filmu i jest wiecej dostepnych filmow niz 0 to wyswietla przycisk wypozycz
                        if (!empty($_SESSION['uzytkownik']) && mysqli_num_rows($czy_uzytkownik_posiada) == 0 && $row['ilosc'] > 0)
                        {
                            echo "
                            <form method='POST'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <input type='hidden' name='action' value='wypozycz'>
                                <button type='submit'>Wypożycz</button>
                            </form>";
                        }
                        echo "
                        </td>
                        <td>";
                        //jezeli uzytkownik wypozyczyl film to wyswietla przycisk zwroc
                        if (!empty($_SESSION['uzytkownik']) && mysqli_num_rows($czy_uzytkownik_posiada) > 0)
                        {
                            echo "
                            <form method='POST'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <input type='hidden' name='action' value='zwroc'>
                                <button type='submit'>Zwróć</button>
                            </form>";
                        }
                        echo "
                        </td>
                        </tr>";
                    }
                    
                    ?>
                </table>
            </div>
        </section>
        <footer>
            <p>Stronę wykonał Matusiewicz Przemysław</p>
        </footer>
    <?php mysqli_close($connect); ?>
    </body>
</html>