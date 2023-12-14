import { useState } from "react";
import axios from "axios";

// eslint-disable-next-line react/prop-types
export function LoginForm({sendData}) {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [loginMessage, setLoginMessage] = useState("");

  const siteURL = "http://localhost/kalanailsmenu"
  
  const handleLogin = async (e) => {
    e.preventDefault();
    try {
      const response = await axios.post(
        `${siteURL}/backendPHP/login.php`,
        {
          username: username,
          password: password,
        }
      );
      if (response.status === 200 && response.data.message === "Login Exitoso") {
        const data = response.data;
        localStorage.setItem('token', data.token); 
        setLoginMessage(response.data.message);
        sendData(data);
      }
    } 
    catch (error) {
      const responseText = JSON.parse(error.request.responseText).message; //Obtiene el mensaje de respuesta desde el servidor y lo almacena en la variable
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


