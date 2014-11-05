using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Xml.Linq;
using VanityMiami.ViewModels;

namespace VanityMiami.Helper
{
    public static class MyXmlParser
    {
        public static MedicalStaff ParseDoctor(XElement element)
        {
            return BaseParse<MedicalStaff>("Id,Name,Url,Photo", element);
        }   

        public static ProcedureViewModel ParseProcedure(XElement procElement)
        {
            return BaseParse<ProcedureViewModel>("Id,Url,Name,Type,HasGallery,BelongToMenu,Slide", procElement);
        }

        public static GalleryImage ParseGalleryImage(XElement imageElement)
        {
            return BaseParse<GalleryImage>("Id,Alt,Src,Thumb,TimeName,MedicalStaffId,ProcedureId", imageElement);
        }

        public static T BaseParse<T>(string properties, XElement element) where T : BaseViewModel, new()
        {
            T o = new T();
            foreach (string prop in properties.Split(','))
            {
                var pi = o.GetType().GetProperty(prop);
                Object value = element.Attribute(prop.ToLower()).Value;
                if (value.ToString() == "true" || value.ToString() == "false")
                    value = Convert.ToBoolean(value.ToString());
                pi.SetValue(o, value);
            }
            return o;
        }
    }
}