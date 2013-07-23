<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>{$subject}</title>
    <meta http-equiv="Content-Type" content="text/html; charset={charset}"/>
</head>
<body style="padding: 0px;">
    <h3>{gt text='Hello %s' tag1=$uname}!</h3>
    <p>{gt text='You created your account at "%s" using OpenID. However, "%s" disabled authentication via OpenID now. To login to "%s" next time, please use your username and the randomly generated password from below.' tag1=$sitename tag2=$sitename tag3=$sitename}</p>
    <ul>
        <li>{gt text='Username: %s' tag1=$uname}</li>
        <li>{gt text='Password: %s' tag1=$password}</li>
    </ul>
    <p>{gt text='We apologise for the inconvenience.'}<br />
        <br />
        {gt text='The %s staff' tag1=$sitename}
    </p>
</body>
</html>