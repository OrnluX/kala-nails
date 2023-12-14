import { LoginForm } from "../components/LoginForm";
import { Dashboard } from "../components/Dashboard";
import { LoaderPage } from "../components/LoaderPage";
import { useState, useEffect } from "react";
import axios from "axios";

const siteUrl = "http://localhost/kalanailsmenu";

export const Login = () => {
  const [state, setState] = useState({
    loading: true,
    logged: false,
    error: null,
  });

    const getData = (dataReceived) => {  
      setState((state) => ({ ...state, logged: dataReceived.userData.authorized }));
    };

  const token = localStorage.getItem("token");

  async function verifyToken() {
    try {
      const response = await axios.post(
        `${siteUrl}/backendPHP/validation.php`,
        { token: token }
      );

      if (response.status === 200 && response.data.message === "token verificado") {
        const token1 = response.data.dbToken;
        const token2 = response.data.token;

        if (token1 === token2) {
          setState((state) => ({ ...state, logged: true }));
        } 

      } else {
        throw new Error("Token verification failed");
      }
    } catch (error) {
      console.log(error);
    } finally {
      setState((state) => ({ ...state, loading: false}));
      
    }
  }

  useEffect(() => {
    if (token != null) {
      verifyToken();
    } else {
      setState((state) => ({ ...state, loading: false}));
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return (
    <>
      {state.loading ? (
        <LoaderPage />
      ) : (
        <>
          {state.error ? (
            <div>Error: {state.error.message}</div>
          ) : (
            <>
              {state.logged ? <Dashboard /> : <LoginForm  sendData={getData}/>}
            </>
          )}
        </>
      )}
    </>
  );
};
