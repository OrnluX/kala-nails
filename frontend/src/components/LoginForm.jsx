import { useState } from "react";
import axios from "axios";

function LoginForm() {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  // const [isLoggedIn, setIsLoggedIn] = useState(false);
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
      if (
        response.status === 200 &&
        response.data.message === "Login Exitoso"
      ) {
        const token = response.data.token;
        localStorage.setItem("token", token);
        // setIsLoggedIn(true);
        setLoginMessage(response.data.message);
      }
    } catch (error) {
      // setIsLoggedIn(false);
      const codigoError = error.response.status;
      const responseText = JSON.parse(error.request.responseText).message;
      switch (codigoError) {
        case 401:
          console.error(error);
          // setIsLoggedIn(false);
          setLoginMessage(responseText);
          break;
        case 404:
          console.error(error);
          // setIsLoggedIn(false);
          setLoginMessage(responseText);
          break;
        case 400:
          console.error(error);
          // setIsLoggedIn(false);
          setLoginMessage(responseText);
          break;

        default:
          console.error(error);
          setLoginMessage("Error conectando al servidor!");
          break;
      }
    }
  };

  return (
    <div>
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
