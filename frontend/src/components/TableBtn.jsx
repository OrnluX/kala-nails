import { AiFillEdit } from "react-icons/ai";
import { AiFillDelete } from "react-icons/ai";

export const TableBtn = ({purpose, productInfo, deleteBtn}) => {

  const handleClickInDelete = () => {
    const infoElement = {
      productName: productInfo.name,
      productId: productInfo.id
    };
    deleteBtn(infoElement);
  } 

  return (
    purpose == 'edit'
    ? (
      <button id={`edit_product-${productInfo.id}`}>
        <AiFillEdit/>
      </button>
    )
    : (
      <button onClick={handleClickInDelete} id={`delete_product-${productInfo.id}`}>
        <AiFillDelete/>
      </button>
    )
  )
}
