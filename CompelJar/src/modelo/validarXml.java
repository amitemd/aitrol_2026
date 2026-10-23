/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package modelo;

import java.io.File;
import javax.xml.XMLConstants;
import javax.xml.transform.Source;
import javax.xml.transform.stream.StreamSource;
import javax.xml.validation.Schema;
import javax.xml.validation.SchemaFactory;
import javax.xml.validation.Validator;
import java.io.IOException;
import org.apache.xmlbeans.XmlObject;
import org.xml.sax.SAXException;

/**
 *
 * @author USER
 */
public class validarXml {

    /**
     * Para crear un Schema a partir de un XSD.
     */
    private static SchemaFactory schemaFactory;
    public static String mensaje = "";
    static {
        schemaFactory = SchemaFactory.newInstance(XMLConstants.W3C_XML_SCHEMA_NS_URI);
    }

    public static void validarXml(String pathXsd, XmlObject xml) throws IOException, SAXException {
        try {
            File file = new File(pathXsd);
            Schema schema = schemaFactory.newSchema(file);
            Validator validator = schema.newValidator();
            Source source = new StreamSource(xml.newInputStream());
            validator.validate(source);
        } catch (Exception e) {
            mensaje = e.toString();
            System.out.println(e.toString());
        }
    }
    
}
