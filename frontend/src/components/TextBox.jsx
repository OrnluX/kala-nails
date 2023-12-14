import '../assets/css/components/TextBox.css'

export const TextBox = ({elementToDelete, cancellDelete}) => {

  return (
    <div className='overlay'>
        <dialog className="dialog_textBox" open>
          <p>{`¿Desea eliminar el producto "${elementToDelete.productName}"?`}</p>
          <button className='redBtn'>Eliminar</button>
          <button onClick={cancellDelete} className='greenBtn'>Cancelar</button>
        </dialog>
    </div>
  )
}
