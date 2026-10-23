package modelo;

import es.mityc.firmaJava.libreria.utilidades.UtilidadTratarNodo;
import es.mityc.firmaJava.libreria.xades.DataToSign;
import es.mityc.firmaJava.libreria.xades.FirmaXML;
import es.mityc.javasign.pkstore.CertStoreException;
import es.mityc.javasign.pkstore.IPKStoreManager;
import es.mityc.javasign.pkstore.keystore.KSStore;
import java.io.*;
import java.security.KeyStore;
import java.security.KeyStoreException;
import java.security.NoSuchAlgorithmException;
import java.security.PrivateKey;
import java.security.Provider;
import java.security.cert.*;
import java.util.ArrayList;
import java.util.List;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;
import org.apache.commons.codec.binary.Base64;
import org.w3c.dom.Document;
import org.xml.sax.SAXException;

/**
 * <p>
 * Clase base que deberían extender los diferentes ejemplos para realizar firmas
 * XML.
 * </p>
 *
 */
public abstract class GenericXMLSignature {

    // <editor-fold defaultstate="collapsed" desc="Variables estaticas">
    public static String Nombre_Comprobante = "compel";
    public static String Simple_Claimed_Role = "compel";
    public static String OUTPUT_DIRECTORY = "";
    public static String INPUT_DIRECTORY = "";

    public static List<String> Mensajes = new ArrayList<>();

    public static void AgregarMensaje(String mensaje) {
        AgregarMensaje(mensaje, "");
    }

    public static void AgregarMensaje(String mensaje, String error) {
        if (Mensajes == null) {
            Mensajes = new ArrayList<>();
        }
        if (error != null) {
            if (error.trim() != "") {
                error = "\n" + error;
            }
        }
        Mensajes.add(mensaje + error);
    }

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="Propiedades">
    private Document _docSigned = null;
    private DataToSign _dataToSign = null;

    public Document getDocSigned() {
        return _docSigned;
    }

    public void setDocSigned(Document _docSigned) {
        this._docSigned = _docSigned;
    }

    public DataToSign getDataToSign() {
        return _dataToSign;
    }

    public void setDataToSign(DataToSign _dataToSign) {
        this._dataToSign = _dataToSign;
    }

    private String _comprobante = "";

    public String getComprobante() {
        return _comprobante;
    }

    public void setComprobante(String comprobante) {
        this._comprobante = comprobante;
    }

    public String getComprobanteFirmado() {
        String resource = "";
        if (getDocSigned() != null) {
        }
        resource = obtenerDocumento(getDocSigned());
        return resource;
    }

    public String getComprobanteFirmadoBase64() {
        String resource = getComprobanteFirmado();
        if (resource.equals("")) {
            return "";
        }
        byte[] encoded = Base64.encodeBase64(resource.getBytes());
        String _docBase64 = new String(encoded);
        return _docBase64;
    }

    public String getComprobanteSoap() {
        String resource = getComprobanteFirmadoBase64();
        if (resource.equals("")) {
            return "";
        }
        String soap = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:ec=\"http://ec.gob.sri.ws.recepcion\">"
                + "\n" + "   <soapenv:Header/>"
                + "\n" + "   <soapenv:Body>"
                + "\n" + "      <ec:validarComprobante>"
                + "\n" + "         <xml>" + resource + "</xml>"
                + "\n" + "      </ec:validarComprobante>"
                + "\n" + "   </soapenv:Body>"
                + "\n" + "</soapenv:Envelope>";
        return soap;
    }

    // </editor-fold>
    /**
     * <p>
     * Almacén PKCS12 con el que se desea realizar la firma
     * </p>
     */
    public static String PKCS12_RESOURCE = ""; //"src/config/certificado_empresa.p12";

    /**
     * <p>
     * Constraseña de acceso a la clave privada del usuario
     * </p>
     */
    public static String PKCS12_PASSWORD = ""; //1234567890";

    /**
     * <p>
     * Ejecución del ejemplo. La ejecución consistirá en la firma de los datos
     * creados por el método abstracto <code>createDataToSign</code> mediante el
     * certificado declarado en la constante <code>PKCS12_FILE</code>. El
     * resultado del proceso de firma será almacenado en un fichero XML en el
     * directorio correspondiente a la constante <code>OUTPUT_DIRECTORY</code>
     * del usuario bajo el nombre devuelto por el método abstracto
     * <code>getSignFileName</code>
     * </p>
     */
    public void executeCertificateFile() {
        // Obtencion del gestor de claves
        IPKStoreManager storeManager = getPKStoreManager();
        if (storeManager == null) {
            AgregarMensaje("El gestor de claves no se ha obtenido correctamente");
            return;
        }

        // Obtencion del certificado para firmar. Utilizaremos el primer certificado del almacen.
        X509Certificate certificate = getFirstCertificate(storeManager);
        if (certificate == null) {
            AgregarMensaje("No existe ningún certificado para firmar");
            return;
        }

        // Obtención de la clave privada asociada al certificado
        PrivateKey privateKey;
        try {
            privateKey = storeManager.getPrivateKey(certificate);
        } catch (CertStoreException ex) {
            AgregarMensaje("Error al acceder al almacén", ex.getMessage());
            return;
        }

        // Obtención del provider encargado de las labores criptográficas
        Provider provider = storeManager.getProvider(certificate);

        // Creación del objeto que contiene tanto los datos a firmar como la configuración del tipo de firma
        _dataToSign = createDataToSign();

        //Creación del objeto encargado de realizar la firma
        FirmaXML firma = new FirmaXML();
        try {
            Object[] res = firma.signFile(certificate, _dataToSign, privateKey, provider);
            _docSigned = (Document) res[0];
        } catch (Exception ex) {
            AgregarMensaje("Error realizando la firma", ex.getMessage());
        }
    }

    /**
     * <p>
     * Crea el objeto DataToSign que contiene toda la información de la firma
     * que se desea realizar. Todas las implementaciones deberán proporcionar
     * una implementación de este método
     * </p>
     *
     * @return El objeto DataToSign que contiene toda la información de la firma
     * a realizar
     */
    protected abstract DataToSign createDataToSign();

    /**
     * <p>
     * Nombre del fichero donde se desea guardar la firma generada. Todas las
     * implementaciones deberán proporcionar este nombre.
     * </p>
     *
     * @return El nombre donde se desea guardar la firma generada
     */
    protected abstract String getSignatureFileName();

    /**
     * <p>
     * Nombre del fichero donde se leen los datos del compropante
     * </p>
     *
     * @return El nombre donde se desea lee el _comprobante
     */
    protected abstract String getFileDocName();

    /**
     * <p>
     * Devuelve el gestor de claves que se va a utilizar
     * </p>
     *
     * @return El gestor de claves que se va a utilizar</p>
     */
    private IPKStoreManager getPKStoreManager() {
        try {
            KeyStore ks = KeyStore.getInstance("PKCS12");
            InputStream filePKCS12 = new FileInputStream(PKCS12_RESOURCE);
            ks.load(filePKCS12, PKCS12_PASSWORD.toCharArray());
            IPKStoreManager storeManager = new KSStore(ks, new PassStoreKS(PKCS12_PASSWORD));
            return storeManager;
        } catch (KeyStoreException ex) {
            AgregarMensaje("No se puede generar KeyStore PKCS12", ex.getMessage());
        } catch (NoSuchAlgorithmException ex) {
            AgregarMensaje("No se puede generar KeyStore PKCS12", ex.getMessage());
        } catch (CertificateException ex) {
            AgregarMensaje("No se puede generar KeyStore PKCS12", ex.getMessage());
        } catch (IOException ex) {
            AgregarMensaje("No se puede generar KeyStore PKCS12", ex.getMessage());
        }
        return null;
    }

    /**
     * <p>
     * Recupera el primero de los certificados del almacén.
     * </p>
     *
     * @param storeManager Interfaz de acceso al almacén
     * @return Primer certificado disponible en el almacén
     */
    private X509Certificate getFirstCertificate(final IPKStoreManager storeManager) {
        try {
            X509Certificate certificate = null;
            List<X509Certificate> certs = storeManager.getSignCertificates();
            if ((certs == null) || (certs.isEmpty())) {
                AgregarMensaje("Lista de certificados vacía");
            } else {
                certificate = certs.get(0);
            }
            return certificate;
        } catch (CertStoreException ex) {
            AgregarMensaje("Fallo obteniendo listado de certificados", ex.getMessage());
        }
        return null;
    }

    /**
     * <p>
     * Devuelve el <code>Document</code> correspondiente al
     * <code>resource</code> pasado como parámetro
     * </p>
     *
     * @param resource El recurso que se desea obtener
     * @return El <code>Document</code> asociado al <code>resource</code>
     */
    protected Document obtenerDocumento(InputStream resource) {
        try {
            DocumentBuilderFactory dbf = DocumentBuilderFactory.newInstance();
            dbf.setNamespaceAware(true);
            Document doc = dbf.newDocumentBuilder().parse(resource);
            return doc;
        } catch (ParserConfigurationException ex) {
            AgregarMensaje("Error al leer el documento", ex.getMessage());
        } catch (SAXException ex) {
            AgregarMensaje("Error al leer el documento", ex.getMessage());
        } catch (IOException ex) {
            AgregarMensaje("Error al leer el documento", ex.getMessage());
        } catch (IllegalArgumentException ex) {
            AgregarMensaje("Error al leer el documento", ex.getMessage());
        }
        return null;
    }

    /**
     * <p>
     * Devuelve el contenido del documento XML correspondiente al
     * <code>resource</code> pasado como parámetro
     * </p> como un <code>String</code>
     *
     * @param resource El recurso que se desea obtener
     * @return El contenido del documento XML como un <code>String</code>
     */
    protected String obtenerDocumento(Document resource) {
        try {
            TransformerFactory tfactory = TransformerFactory.newInstance();
            StringWriter stringWriter = new StringWriter();
            Transformer serializer = tfactory.newTransformer();
            serializer.transform(new DOMSource(resource), new StreamResult(stringWriter));
            String result = stringWriter.toString();
            return result;
        } catch (TransformerException ex) {
            AgregarMensaje("Error al leer el documento", ex.getMessage());
        }
        return "";
    }

    /**
     * <p>
     * Escribe el documento a un fichero.
     * </p>
     *
     * @param document El documento a imprmir
     * @param pathfile El path del fichero donde se quiere escribir.
     */
    private void guardarArchivoXml(Document document, String pathfile) {
        try {
            FileOutputStream fos = new FileOutputStream(pathfile);
            UtilidadTratarNodo.saveDocumentToOutputStream(document, fos, true);
            fos.close();
        } catch (FileNotFoundException ex) {
            AgregarMensaje("Error al guardar el documento", ex.getMessage());
        } catch (IOException ex) {
            AgregarMensaje("Error al leer documento", ex.getMessage());
        }
    }

    /**
     * <p>
     * Escribe el documento a un fichero. Esta implementacion es insegura ya que
     * dependiendo del gestor de transformadas el contenido podría ser alterado,
     * con lo que el XML escrito no sería correcto desde el punto de vista de
     * validez de la firma.
     * </p>
     *
     * @param document El documento a imprmir
     * @param pathfile El path del fichero donde se quiere escribir.
     */
    @SuppressWarnings("unused")
    private void guardarArchivoXmlUnsafeMode(Document document, String pathfile) {
        try {
            TransformerFactory tfactory = TransformerFactory.newInstance();
            Transformer serializer = tfactory.newTransformer();
            serializer.transform(new DOMSource(document), new StreamResult(new File(pathfile)));
        } catch (TransformerException ex) {
            AgregarMensaje("Error al guardar el documento", ex.getMessage());
        }
    }

}
