<?PHP

include('autoexec.cfg.php'); #database connection config
include('utils.php'); #str_sanitize and other maintenance functions.

$total_human_count = 0;
$total_human_age = 0;

$db_conn_string = $db_type.":host=".$db_host.";dbname=".$db_name;

try
{
    $db_conn = new PDO($db_conn_string, $db_user, $db_pass,[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}
catch (PDOException $e)
{
    echo($e->getMessage());
    exit();
}

if (isset($_GET["mode"]) and isset($_GET["id"]))
{
    $mode=$_GET["mode"];
    $id=intval($_GET["id"]);

    if ($mode == "delete")
    {
        try
        {
            $query = "DELETE FROM humans_list WHERE id=:id";
            $query_statement = $db_conn->prepare($query);
            $query_statement -> execute(array(':id' => $id));
            $count = $query_statement -> rowCount();
            $query_statement = NULL;
        }
        catch (PDOException $e)
        {
            echo($e->getMessage());
            exit();
        }

    }
}

if (isset($_POST["HumanName"]) and isset($_POST["HumanAge"]))
{   
    $name = $_POST["HumanName"];
    $name = str_sanitize($name);

    $age = intval($_POST["HumanAge"]);
        
    if ($age<0) $age=0;
    if ($age>1000) $age=999;

    if (strlen($name) > 255) $name=substr($name,0,254);

    if ($name == "")
    {
        echo "
        <script>
        alert('Заполните поле \"Имя\".');
        </script>
        ";

        #Вариант 2, но с перезагрузкой страницы:
        #echo "
        #<script>
        #alert('Заполните поле \"Имя\".');
        #window.open('".$_SERVER["PHP_SELF"]."?reenterAge=$age','_self',false);
        #</script>
        #";
    }
    else 
    {
        echo "Ввод: ".$name." ".$age."<BR>";

        try 
        {
            $query = "SELECT Id,Name FROM humans_list WHERE Name=:name LIMIT 1";
            
            $query_statement = $db_conn->prepare($query);
            $query_statement -> execute(array(':name' => $name));

            $humans_list = $query_statement -> fetchAll();
            
            $count = count($humans_list);
            #или так: $count=$query_result->fetchColumn();

            $query_statement = NULL;
            
            echo "Найдено записей: ".$count."<BR>";
            
            if ($count == 0)
            {
                try
                {
                    $query = "INSERT INTO humans_list(Name,Age) VALUES(:name,:age)";
                    $query_statement = $db_conn->prepare($query);
                    $query_statement -> execute(array(':name' => $name, ':age' => $age));
                    $count = $query_statement -> rowCount();
                    $query_statement = NULL;
                    unset($name);
                    unset($age);

                    echo "Добавлено записей: ".$count."<BR>";
                }
                catch (PDOException $e)
                {
                    echo($e->getMessage());
                    exit();
                }

            }
            else
            {
                foreach($humans_list as $human)
                {
                    try
                    {   
                        $query = "UPDATE humans_list SET Age=:age WHERE id=:id";
                        $query_statement = $db_conn->prepare($query);
                        $query_statement -> execute( array(':age' => $age, ':id' => $human["Id"]) );
                        $count = $query_statement->rowCount();
                        $query_statement = NULL;
                        unset($name);
                        unset($age);                        
                        
                        echo "Обновлено записей: ".$count."<BR>";
                        
                    }
                    catch (PDOException $e)
                    {
                        echo($e->getMessage());
                        exit();
                    }
                }
            }    
        }
        catch (PDOException $e)
        {
            echo($e->getMessage());
            exit();
        }
    }
}



echo "
<!DOCTYPE html>
<html>
<head>
<title>
PHP test (simple human list)
</title>
<meta name=\"color-scheme\" content=\"light dark\">
<style>
h1 {text-align: center;}
</style>
</head>
<body>
";

echo "<h1>Перепись</h1>";

if ((isset($age) and isset($name) and $name == "") or isset($_GET["reenterAge"]))
{
    echo "Заполните поле имя!
    <BR>
    <BR>";
}

$reenterAge="";

if (isset($age)) 
{
    $reenterAge = ($age <= 0) ? "" : $age;
}
elseif (isset($_GET["reenterAge"])) 
{
    $reenterAge=intval($_GET["reenterAge"]);
}

echo "
<form method=\"POST\" action=\"".$_SERVER["PHP_SELF"]."\">
  <label for=\"HumanName\">Имя:</label>
  <input type=\"text\" id=\"HumanName\" name=\"HumanName\"><br><br>
  <label for=\"HumanAge\">Возраст:</label>
  <input type=\"text\" id=\"HumanAge\" name=\"HumanAge\" value=\"$reenterAge\"><br><br>
  <input type=\"hidden\" name=\"mode\" value=\"update\">
  <input type=\"submit\" value=\"Отправить\">
</form>
";

echo "
<p>Для обновления возраста введите такое же имя, которое уже есть в базе данных (см. список ниже).</p>
<p>Для удаление записи нажмите на значок [X] справа от записи.</p>
";

try
{
    $query = "SELECT * FROM humans_list";
    $query_result = $db_conn->query($query);

    $humans_list = $query_result->fetchAll();

    $total_human_count = count($humans_list);
        
    echo "<table>";
    
    foreach($humans_list as $human)
    {
        echo "<tr>";
        echo "<td>";
        echo $human['Name'];
        echo "<td>";
        echo $human['Age'];
        echo "<td>";
        echo "<a href=\"".$_SERVER["PHP_SELF"]."?mode=delete&id=".$human['Id']."\" onclick=\"return confirm('Вы уверены что хотите удалить запись ".str_sanitize($human['Name'])."?')\"> [X] </a>";
        echo "</tr>";

        $total_human_age += intval($human['Age']);

    }

    echo "</table>";
}
catch (PDOException $e)
{
    echo($e->getMessage());
    exit();
}

echo "<p>";
echo "<table>";
echo "<tr>";
echo "<td>Переписано человек: ".$total_human_count;
echo "<tr>";
echo "<td>Общий возраст: ".$total_human_age;
echo "</table>";
echo "</p>";

echo "
</body>
</html>
";
?>