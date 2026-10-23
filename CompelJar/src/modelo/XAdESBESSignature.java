package modelo;

import es.mityc.firmaJava.libreria.xades.DataToSign;
import es.mityc.firmaJava.libreria.xades.DatosFirma;
import es.mityc.firmaJava.libreria.xades.ResultadoValidacion;
import es.mityc.firmaJava.libreria.xades.ValidarFirmaXML;
import es.mityc.firmaJava.libreria.xades.XAdESSchemas;
import es.mityc.firmaJava.libreria.xades.errores.FirmaXMLError;
import es.mityc.firmaJava.role.SimpleClaimedRole;
import es.mityc.javasign.EnumFormatoFirma;
import es.mityc.javasign.ts.TimeStampValidator;
import es.mityc.javasign.xml.refs.AllXMLToSign;
import es.mityc.javasign.xml.refs.InternObjectToSign;
import es.mityc.javasign.xml.refs.ObjectToSign;
import java.io.ByteArrayInputStream;
import java.io.File;
import java.io.InputStream;
import java.nio.charset.StandardCharsets;
import java.security.cert.X509Certificate;
import java.util.ArrayList;
import java.util.Iterator;
import org.w3c.dom.Document;

/**
 * <p>
 * Clase de ejemplo para la firma XAdES-BES enveloped de un documento
 * </p>
 * <p>
 * Para realizar la firma se utilizará el almacén PKCS#12 definido en la
 * constante <code>GenericXMLSignature.PKCS12_FILE</code>, al que se accederá
 * mediante la password definida en la constante
 * <code>GenericXMLSignature.PKCS12_PASSWORD</code>. El directorio donde quedará
 * el archivo XML resultante será el indicado en al constante
 * <code>GenericXMLSignature.OUTPUT_DIRECTORY</code>
 * </p>
 *
 */
public class XAdESBESSignature extends GenericXMLSignature {

    // <editor-fold defaultstate="collapsed" desc="Propiedades">
    private boolean isValido;
    private String nivelValido;
    private X509Certificate certificado;
    private DatosFirma firma;
    private ArrayList<String> datosFirmados;

    public boolean IsValido() {
        return isValido;
    }

    public void setIsValido(boolean isValido) {
        this.isValido = isValido;
    }

    public String getNivelValido() {
        return nivelValido;
    }

    public void setNivelValido(String nivelValido) {
        this.nivelValido = nivelValido;
    }

    public X509Certificate getCertificado() {
        return certificado;
    }

    public void setCertificado(X509Certificate certificado) {
        this.certificado = certificado;
    }

    public DatosFirma getFirma() {
        return firma;
    }

    public void setFirma(DatosFirma firma) {
        this.firma = firma;
    }

    public ArrayList<String> getDatosFirmados() {
        return datosFirmados;
    }

    public void setDatosFirmados(ArrayList<String> datosFirmados) {
        this.datosFirmados = datosFirmados;
    }

    // </editor-fold>
    @Override
    protected DataToSign createDataToSign() {
        DataToSign datosAFirmar = new DataToSign();
        datosAFirmar.setXadesFormat(es.mityc.javasign.EnumFormatoFirma.XAdES_BES);
        datosAFirmar.setEsquema(XAdESSchemas.XAdES_132);
        datosAFirmar.setXMLEncoding("UTF-8");
        datosAFirmar.setEnveloped(true);
        datosAFirmar.addObject(new ObjectToSign(new InternObjectToSign("comprobante"), "compel", null, "text/xml", null));
        datosAFirmar.setParentSignNode("comprobante");

        InputStream resource = new ByteArrayInputStream(getComprobante().getBytes(StandardCharsets.UTF_8));
        Document docToSign = obtenerDocumento(resource);
        datosAFirmar.setDocument(docToSign);
        return datosAFirmar;
    }

    @Override
    protected String getSignatureFileName() {
        String nombre = Nombre_Comprobante;
        String result = "firmado_" + (nombre.equals("") ? "test" : nombre) + ".xml";
        return result;
        //return SIGN_FILE_NAME;
    }

    @Override
    protected String getFileDocName() {
        String nombre = Nombre_Comprobante;
        String result = (nombre.equals("") ? "test" : nombre) + ".xml";
        return result;
    }

    public String getPathFileSignatureName() {
        String result = OUTPUT_DIRECTORY + File.separatorChar + getSignatureFileName();
        return result;
    }

    public String getPathFileDocName() {
        String result = OUTPUT_DIRECTORY + File.separatorChar + getFileDocName();
        return result;
    }

    /**
     * Método que realiza la validación de firma digital XAdES a un fichero y
     * muestra el resultado
     *
     * @param doc
     */
    public void validarFichero(Document doc) {
        this.isValido = false;

        if (doc == null) {
            AgregarMensaje("No se pudo leer documento");
            return;
        }

        // Se instancia el validador y se realiza la validación
        ArrayList<ResultadoValidacion> results = null;
        try {
            ValidarFirmaXML vfXml = new ValidarFirmaXML();
            results = vfXml.validar(doc, "./", null, new TimeStampValidator());
        } catch (FirmaXMLError ex) {
            AgregarMensaje("No se pudo validar la firma", ex.getMessage());
        }

        // Se muestra por consola el resultado de la validación
        ResultadoValidacion result = null;
        Iterator<ResultadoValidacion> it = results.iterator();
        while (it.hasNext()) {
            result = it.next();
            boolean isValid = result.isValidate();
            if (isValid) {
                this.isValido = result.isValidate();
                this.nivelValido = result.getNivelValido();
                this.certificado = (X509Certificate) result.getDatosFirma().getCadenaFirma().getCertificates().get(0);
                this.firma = result.getDatosFirma();
                this.datosFirmados = result.getFirmados();
                // El método getNivelValido devuelve el último nivel XAdES válido
                String mensaje = "La firma es válida.\n" + result.getNivelValido()
                        + "\nCertificado: " + ((X509Certificate) result.getDatosFirma().getCadenaFirma().getCertificates().get(0)).getSubjectDN()
                        + "\nFirmado el: " + result.getDatosFirma().getFechaFirma()
                        + "\nEstado de confianza: " + result.getDatosFirma().esCadenaConfianza()
                        + "\nNodos firmados: " + result.getFirmados();
                AgregarMensaje(mensaje);
            } else {
                // El método getLog devuelve el mensaje de error que invalidó la firma
                AgregarMensaje("Firma inválida", result.getLog());
            }
        }
    }

}
