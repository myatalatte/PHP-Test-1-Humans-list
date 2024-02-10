<?PHP

#*******************
# str_sanitize($str)
#*******************
#
#������� �������� ������� � ������, ����� ��������� ����������� ��������� ����.
#
#��� ����� ������� ������� '," � ;, ����� ��������� ����������� SQL-inject'� � XSS.
#������, ���� ��������� ������� ' � " ��������� $str = htmlentities/htmlspecialchars($str, ENT_QUOTES), 
#�� ������-�� �������� ������� ������� � � ������� �� ��������, ������ - ���������,
#�������� ��� ���-�� ������� � ����������, ������������ � ������ ������, �� ���� ������ UTF-8, ������ �� ��������.
#������� ������ ������� ������� ������� ',",; � ������� str_replace, ������ ������ �� ������� � ����� � �������.
#!!!!!
#��� ����, ���� ��������� htmlentities/htmlspecialchars �� str_replace, �� str_replace �� �����������!
#������ - ���� ���������.
#!!!!!
#������� �������� htmlspecialchars ������ ����� str_replace, ������ �� ������ ������ (���� ��������� - ���� �� �����).
function str_sanitize($str)
{
    $str = (string)$str;

    $str = trim(str_replace(array("'", '"', ";"), "", $str));
    $str = strip_tags($str);

    #�� ������ ������ �������������:
    $str = htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);

    #PS. �� ��������:
    #$str = htmlspecialchars($str);
    #�� �������� 2:
    #$str = htmlspecialchars($str, ENT_QUOTES)
    #�� �������� ���� ���...:
    #$str = htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, "UTF-8", true);
    return $str;
}
?>