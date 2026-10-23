/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package edu.sri.compel;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.StringReader;
import java.io.StringWriter;
import java.io.Writer;
import java.util.ArrayList;
import java.util.List;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;
import org.w3c.dom.Document;
import org.xml.sax.InputSource;

/**
 *
 * @author User
 */
public class CompelJar {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        String result = "";
        String certificado = "";
        String clave = "";
        String dataDoc = "";
        boolean isPathXml = false;
        String comprobante = "";

        boolean isError = args != null ? args.length < 3 : true;
        if (!isError) {
            certificado = String.format("%s", args[0]).trim();
            clave = String.format("%s", args[1]).trim();
            dataDoc = String.format("%s", args[2]).trim();
            isPathXml = args.length >= 4 ? String.format("%s", args[3]).trim().equals("1") : false;
            if (!isPathXml) {
                Document xmlDataDoc = CompelJar.ConvertStringToDocument(dataDoc);
                if (xmlDataDoc != null) {
                    comprobante = CompelJar.ConvertDocumentToString(xmlDataDoc);
                }
            } else {
                comprobante = CompelJar.ReadDocument(dataDoc);
                File fichero = new File(dataDoc);
                fichero.delete();
            }
        }
        if (certificado.equals("") || clave.equals("") || comprobante.equals("")) {
            CompelFirmado compelSign = new CompelFirmado(certificado, clave, dataDoc);
            List<String> mensajes = new ArrayList<>();
            mensajes.add("Parametros incorrectos");
            mensajes.add("Usage: certificado clave [dataDoc/pathXml] [isPathxml:optional]");
            result = compelSign.obtenerRespuestaXml(false, "", mensajes);
            System.out.println(result);
            System.exit(-1);
        }

        result = CompelJar.ObtenerFirmado(certificado, clave, comprobante);
        if (!isPathXml) {
            System.out.println(result);
        } else {
            CompelJar.WriteDocument(dataDoc, result);
        }
    }

    public static String ObtenerFirmado(String certificado, String clave, String dataDoc) {
        CompelFirmado compelSign = new CompelFirmado(certificado, clave, dataDoc);
        compelSign.Generar();
        String result = compelSign.getDataDocSign();
        return result;
    }

    public static String ReadDocument(String path) {
        String result = "";
        BufferedReader br = null;
        try {
            br = new BufferedReader(new InputStreamReader(new FileInputStream(path), "utf-8"));

            String linea;
            while ((linea = br.readLine()) != null) {
                result += linea + "\n";
            }
        } catch (IOException e) {
            result = "";
        } finally {
            try {
                if (br != null) {
                    br.close();
                }
            } catch (IOException e2) {
                result = "";
            }
        }
        return result;
    }

    public static boolean WriteDocument(String path, String contenido) {
        boolean result = false;
        Writer write = null;
        try {
            write = new BufferedWriter(new OutputStreamWriter(new FileOutputStream(path), "UTF8"));
            write.write(contenido);
            result = true;
        } catch (IOException e) {
            result = false;
        } finally {
            try {
                if (write != null) {
                    write.close();
                }
            } catch (IOException ex) {
                result = false;
            }
        }
        return result;
    }

    public static Document ConvertStringToDocument(String xmlStr) {
        try {
            DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
            DocumentBuilder builder = factory.newDocumentBuilder();
            Document doc = builder.parse(new InputSource(new StringReader(xmlStr)));
            return doc;
        } catch (Exception e) {
        }
        return null;
    }

    public static String ConvertDocumentToString(Document doc) {
        try {
            TransformerFactory tf = TransformerFactory.newInstance();
            Transformer transformer = tf.newTransformer();
            // below code to remove XML declaration
            // transformer.setOutputProperty(OutputKeys.OMIT_XML_DECLARATION, "yes");
            transformer.setOutputProperty("{http://xml.apache.org/xslt}indent-amount", "2");
            transformer.setOutputProperty(OutputKeys.INDENT, "yes");
            transformer.setOutputProperty(OutputKeys.STANDALONE, "no");
            StringWriter writer = new StringWriter();
            transformer.transform(new DOMSource(doc), new StreamResult(writer));
            String output = writer.getBuffer().toString();
            return output;
        } catch (TransformerException e) {
        }
        return "";
    }
}
