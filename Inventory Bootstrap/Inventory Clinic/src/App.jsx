import React from "react";
import "bootstrap/dist/css/bootstrap.min.css";
import AppRoutes from "./routes/AppRoutes";
import { useState, useEffect } from "react";

const App = () => {
  const [isAuth, setIsAuth] = useState(false);

  useEffect(() => {
    const auth = localStorage.getItem("isAuth");
    if (auth === "true") {
      setIsAuth(true);
    }
  }, [isAuth]);

  return (
    <>
      <AppRoutes isAuth={isAuth} setIsAuth={setIsAuth} />
    </>
  );
};

export default App;