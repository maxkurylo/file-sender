<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign in Client</title>
    <style>
        body {
            text-align: center;
            font-family: helvetica;
        }
        label, input {
            font-size: 1.1em;
            padding: 5px;
        }

        input[type="submit"] {
            padding: 6px;
            background-color: #acf;
        }
    </style>
</head>
<body>
    <h1>Sign in or create account</h1>
    <form 
   
    id="user-signin-form"
    >
        <label for="user_login">Login</label>
        <input type="email" name="user_login" id="user_login" placeholder="example@mail.com" value="testuser@gmail.com">

        <label for="user_password">Password</label>
        <input type="password" name="user_password" id="user_password" value="12345678">

        <input type="submit">
    </form>

    <script>
        let form = document.getElementById("user-signin-form");
        form.onsubmit = formSubmit;

        async function formSubmit(e) {
            e.preventDefault();
            const formData = new FormData(form);
            const options = {
                "method": "POST",
                "credentials": "include",
                "body": formData
            }

            const promise = await fetch("server/signin.php", options)
            .then( resp => resp.json() )
            .then( console.log )
            .catch ( console.error );
        }
    </script>
</body>
</html>