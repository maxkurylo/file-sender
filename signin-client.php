<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign in Client</title>
</head>
<body>
    <p>Sign In</p>
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

            const promise = await fetch("../server/signin.php", options)
            .then( resp => resp.json() )
            .then( console.log )
            .catch ( console.error );
        }
    </script>
</body>
</html>