<html>
    <body>
    <div>Estimado(a): </b><hr>
        Se ha creado una cuenta para ud en el Sistema de Gestión del POA.
        <p>Siga el siguiente link para acceder al mismo.
        </p>
        <a href="{{route('login')}}">Sistema Gestión de POA Fedeguayas</a>
        <p>
            El sistema genera una contraseña aleatoria para ud, una vez en el puede cambiarla en el menú de usuario.
        </p>
        <div>
            <p>
                Sus datos de acceso son: <br>
                usuario: {{$user->email}} <br>
                contraseña: {{$pass}} <br>
            </p>
        </div>

    </div>
    </body>
</html>