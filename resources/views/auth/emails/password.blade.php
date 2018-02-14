<p>Click aquí para resetear su contraseña: <a href="{{ $link = url('password/reset', $token).'?email='.urlencode($user->getEmailForPasswordReset()) }}"> {{ $link }} </a></p>
<br>
<p>
    Si no puede hacer click en el link, cópielo y péguelo en un navegador nuevo.
</p>
