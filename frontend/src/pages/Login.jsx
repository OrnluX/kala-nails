import { useState, useEffect} from "react";
import axios from "axios";
import {LoginForm} from "../components/LoginForm";
import {Dashboard} from "../components/Dashboard";
import { LoaderPage } from "../components/LoaderPage";

export const Login = () => {
  
  const [logged, setLogged] = useState(false);
  const [loaded, setLoaded] = useState(false);

  const getData = (dataReceived) => {
    setLogged(dataReceived.userData.authorized);
  };

  useEffect(() => {
    const token = localStorage.getItem('token');
    const tokenVerification = async (tkn) => {
      try {
        const response = await axios.post("http://localhost/kalanailsMenu/backendPHP/validation.php", 
          {token: tkn});
         
          if (response.status === 200 && response.data.message == 'token verificado') {
            const token1 = response.data.dbToken;
            const token2 = response.data.token;
            if (token1 === token2) {
              setLogged(true);
            }
          }
      }
      catch (error) {
        setLogged(false);
      }
      finally {
        setLoaded(true);
      }
    }
    if (token != null) {
      tokenVerification(token);
    } else {
      setLoaded(true);
    }
  }, []);

  return <>
  {!loaded
  ? <LoaderPage/>
  : <div>
    {!logged
    ? <LoginForm sendData={getData} /> 
    : <Dashboard />}
  </div>
  }
  </>;
};
