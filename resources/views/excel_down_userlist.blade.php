
{{header( "Content-type: application/vnd.ms-excel" )}}
{{header( "Content-type: application/vnd.ms-excel; charset=utf-8")}}
{{header( "Content-Disposition: attachment; filename = user_list.xls" )}}
{{header( "Content-Description: PHP7 Generated Data" )}}
<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\">


    <table border='1'>
        <tr>
        <td>이메일</td>
        <td>이름(업체명)</td>
        <td>구분</td>
        <td>휴대폰 번호</td>
        <td>생성일</td>
        <td>최종로그인</td>
        </tr>
        @foreach ($data as $row)
        <tr>
        <td>{{$row['email']}}</td>
        <td>{{$row['name']}}</td>
        <td>{{$row['type']}}</td>
        <td>{{$row['phone']}}</td>
        <td>{{$row['created_at']}}</td>
        <td>{{$row['last_login']}}</td>
        </tr>
        @endforeach
    </table>


