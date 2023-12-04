import { useState } from "react";
import axios from "axios";

// eslint-disable-next-line react/prop-types
function LoginForm({sendToken}) {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [loginMessage, setLoginMessage] = useState("");

  const handleLogin = async (e) => {
    e.preventDefault();
    try {
      const response = await axios.post(
        "http://localhost/kalanailsmenu/backendPHP/login.php",
        {
          username: username,
          password: password,
        }
      );
      console.log(response.data);
      if (response.status === 200 && response.data.message === "Login Exitoso") {
        const token = response.data.userData.token;
        localStorage.setItem("token", token);
        sendToken(token);
        setLoginMessage(response.data.message);
      }
    } catch (error) {
      const responseText = JSON.parse(error.request.responseText).message; //Obtiene el mensaje de respuesta desde el servidor y lo almacena en la variable
      console.error(error);
      setLoginMessage(responseText);
    }
  };

  return (
    <div>
      <h1>Login</h1>
      <form onSubmit={handleLogin}>
        <label>
          Usuario:
          <input
            type="text"
            value={username}
            onChange={(e) => setUsername(e.target.value)}
          />
        </label>
        <label>
          Contraseña:
          <input
            type="password"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />
        </label>
        <button type="submit">Iniciar sesión</button>
      </form>
      <p>{loginMessage}</p>
    </div>
  );
}

export default LoginForm;
