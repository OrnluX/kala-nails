import { TableBtn } from './TableBtn';

export const Table = ({products, handleElement}) => {
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
                {products.map((product) => (
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
                            <TableBtn purpose={'delete'} productInfo={{id: product.id, name: product.nombre}} deleteBtn={handleElement} />
                        </td>
                    </tr>
                ) )}
            </tbody>
            </table>
    </>
  )
};
