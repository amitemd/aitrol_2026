/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package validadorxsd;

import java.io.File;
import java.nio.charset.Charset;
import java.nio.file.Files;
import java.nio.file.Paths;
import java.util.List;
import java.util.stream.Collectors;
import org.apache.xmlbeans.XmlObject;

/**
 *
 * @author User
 */
public class ValidadorXSD {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        String xsdPath = "";
        String xmlPath = "";
        String mensaje = "";
        mensaje = ValidarXSD(args[0],args[1]);
    }
    
    public static String ValidarXSD(String xsdPath, String xmlPath){
        String mensaje = "";
        try {
            if(xsdPath == null){
                xsdPath = "";
            }
            
            if(xmlPath == null){
                xmlPath = "";
            }
            
            if(xsdPath.isEmpty()){
                return "xsd path es vacio";
            }
            
            if(xmlPath.isEmpty()){
                return "xml path es vacio";
            }
            
            File fileSign = new File(xmlPath);
            String xmlSign = convertFileToString(fileSign);
            XmlObject objXML = XmlObject.Factory.parse(xmlSign);
            validarXml.validarXml(xsdPath, objXML);
            mensaje = validarXml.mensaje;
        } catch (Exception e) {
            mensaje = e.toString();
            return mensaje;
        }
        return mensaje;
    }
    
    public static String convertFileToString(File file){
        try {
            if(file.exists()){
                // charset for encoding
                Charset encoding = Charset.defaultCharset();

                // reading all lines of file as List of strings
                List<String> lines = Files.readAllLines(Paths.get(file.getPath()), encoding);

                // converting List<String> to palin string using java 8 api.
                String string = lines.stream().collect(Collectors.joining("\n"));

                // printing the final string.

                return string;
            }
            else{
                return null;
            }
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }
}
