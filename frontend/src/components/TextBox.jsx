import '../assets/css/components/TextBox.css'
import axios from 'axios';


export const TextBox = ({elementToDelete, closeTextBox, dataChanged}) => {

  const onDeleteClick = async () => {
    try {
     
     const response = await axios.delete(`http://localhost/kalanailsmenu/backendPHP/product/product.php?id=${elementToDelete.productId}`);
      
     console.log(response.data.message);
    
    } catch (error) {
      console.log(error)
    
    } finally {
      dataChanged();
      closeTextBox();
    }
    
  }

  return (
    <div className='overlay'>
        <dialog className="dialog_textBox" open>
          <p>{`¿Desea eliminar el producto "${elementToDelete.productName}"?`}</p>
          <p><strong>Este cambio no puede deshacerse</strong></p>
          <button autoFocus onClick={onDeleteClick} className='redBtn'>Eliminar</button>
          <button onClick={closeTextBox} className='greenBtn'>Cancelar</button>
        </dialog>
    </div>
  )
}
