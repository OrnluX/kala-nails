import { TableBtn } from './TableBtn';
import { TextBox } from './TextBox';
import { useEffect, useState } from 'react';

import '../assets/css/components/Table.css';

export const Table = ({data}) => {
    const [showTextBox, setShowTextBox] = useState(false);
    const [deleteElement, setElementDelete] = useState(null);

    /**Función que se pasa como prop al componente TextBox para manejar el click en el botón de cancelar (referente a eliminar un producto o no). Setea el estado de visibilidad de dicho componente hijo.
     * @return VOID
     */
    const handleCancellBtn = () => {
        setShowTextBox(false);
    }

    /**Función que se pasa como prop al componente TableBtn para manejar el evento "click" del botón de eliminar producto. Recibe la información del producto que se desea eliminar y almacena dicha información en un estado "deleteElement"
     * @param OBJECT elementInfo
     * @return VOID
    */
    const handleElementInfo = (elementInfo) => { 
        setElementDelete(elementInfo);
    }
    useEffect(() => { //Este efecto depende del cambio de la información del estado deleteElement. Cuando el componente hijo TableBtn envía la información y se guarda en dicho estado, "showTextBox" cambia a true y se muestra la ventana modal.
        if (deleteElement === null) {
            setShowTextBox(false);
        } else {
            setShowTextBox(true);
        }
    },[deleteElement])

    return (                                
        <>
            <table className='table-products_container'>
            <thead>
                <tr className='table-header_row'>
                    <th 
                        className="table-header_colum bordered_container">
                        Nombre
                    </th>
                    <th 
                        className="table-header_colum bordered_container">
                        Descripción
                    </th>
                    <th 
                        className="table-header_colum bordered_container">
                        Precio
                    </th>
                    <th 
                        className="table-header_colum bordered_container">
                        Imagen
                    </th>
                    <th 
                        className="table-header_colum bordered_container">
                        Editar
                    </th>
                    <th 
                        className="table-header_colum bordered_container">
                        Eliminar
                    </th>
                </tr>
            </thead>
            <tbody>
                {data.map((product) => (
                    <tr className='table-body_row' key={product.id}>
                        <td 
                            className="table-product_info bordered_container">
                            {product.nombre}
                        </td>
                        <td 
                            className='table-product_info bordered_container'>
                            {product.descripcion}
                        </td>
                        <td 
                            className="table-product_info bordered_container">
                            ${product.precio}
                        </td>
                        <td 
                            className='table-img_container bordered_container'>
                            <img className='table-product_img ' src={product.imagen} alt={`Foto de ${product.nombre}`} />
                        </td>
                        <td className='bordered_container'>
                            <TableBtn purpose={'edit'} productInfo={{id: product.id, name: product.nombre}} />
                        </td>
                        <td className='bordered_container' >
                            <TableBtn purpose={'delete'} productInfo={{id: product.id, name: product.nombre}} deleteBtn={handleElementInfo} />
                        </td>
                    </tr>
                ) )}
            </tbody>
            </table>
            {
                showTextBox
                ? (<TextBox elementToDelete={deleteElement} cancellDelete={handleCancellBtn} />)
                : (null)
            }
        </>
  )
}

