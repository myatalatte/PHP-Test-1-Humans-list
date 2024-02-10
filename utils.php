<?PHP

#*******************
# str_sanitize($str)
#*******************
#
#Удаляет ненужные символы в строке, чтобы уменьшить вероятность различных атак.
#
#Нам нужно удалить символы '," и ;, чтобы уменьшить вероятность SQL-inject'а и XSS.
#Кстати, если пробовать удалять ' и " функциями $str = htmlentities/htmlspecialchars($str, ENT_QUOTES), 
#то почему-то удаление двойных кавычек с её помощью не работает, почему - непонятно,
#возможно это как-то связано с кодировкой, используемой в данный момент, но даже указав UTF-8, ничего не меняется.
#Поэтому удалим сначала вручную символы ',",; с помощью str_replace, указав массив из кавычек и точки с запятой.
#!!!!!
#При этом, если выполнить htmlentities/htmlspecialchars ДО str_replace, то str_replace не срабатывает!
#Почему - тоже непонятно.
#!!!!!
#Поэтому выполним htmlspecialchars только после str_replace, просто на всякий случай (если сработает - хуже не будет).
function str_sanitize($str)
{
    $str = (string)$str;

    $str = trim(str_replace(array("'", '"', ";"), "", $str));
    $str = strip_tags($str);

    #на всякий случай дополнительно:
    $str = htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);

    #PS. не работает:
    #$str = htmlspecialchars($str);
    #не работает 2:
    #$str = htmlspecialchars($str, ENT_QUOTES)
    #не работает даже так...:
    #$str = htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, "UTF-8", true);
    return $str;
}
?>