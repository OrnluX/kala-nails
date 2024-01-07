import { LoaderPage } from "./constants/LoaderPage";
import { TableController } from "../controllers/TableController";
import { useState ,useEffect } from "react";
import axios from 'axios';
import '../assets/css/components/Dashboard.css'

const siteUrl = "http://localhost/kalanailsmenu";

export const Dashboard = () => {
  const [loading, setLoading] = useState(true);
  const [products, setProducts] = useState([]);
 
  useEffect(()=>{
    const getData = async () => {
      try {
        const response = await axios.get(
          `${siteUrl}/backendPHP/product/product.php`
        );  
        setProducts(response.data);
      } 
      catch (error) {
        console.log(error);
      } finally {
        setLoading(false);
      }
    }
    getData();
  },[])

  return (
    <>  
        <h1>Dashboard</h1>
        {
          loading ? (
            <LoaderPage/>
          ) : (
          
            <TableController data={products}/>
            
          )
        }
    </>
  )
}

