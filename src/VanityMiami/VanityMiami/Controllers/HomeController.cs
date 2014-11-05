using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using VanityMiami.ViewModels;
using System.Xml.Linq;
using VanityMiami.Helper;

namespace VanityMiami.Controllers
{
    public class HomeController : Controller
    {   
        //Generals
        public ActionResult Index()
        {
            BaseViewModel vm = new BaseViewModel();
            vm.PageTitle = "Cosmetic Surgery Miami | Top Pastic Surgeons - Vanity Cosmetic Surgery";
            XElement indexElement = this.getXmlItemByType("HomePage");
            vm.Meta = Server.MapPath("~/App_Data/Pages/En/" + indexElement.Attribute("url").Value + "/metas.html");

            return View(vm);
        }

        public ActionResult About()
        {
            BaseViewModel vm = new BaseViewModel();
            vm.PageTitle = "About Us | Vanity Cosmetic Surgery";
            XElement aboutElement = this.getXmlItemByType("About");
            vm.Meta = Server.MapPath("~/App_Data/Pages/En/" + aboutElement.Attribute("url").Value + "/metas.html");

            return View(vm);
        }

        public ActionResult Contact()
        {
            BaseViewModel vm = new BaseViewModel();
            vm.PageTitle = "Contact Us | Vanity Cosmetic Surgery";

            XElement contactElement = this.getXmlItemByType("Contact");
            vm.Meta = Server.MapPath("~/App_Data/Pages/En/" + contactElement.Attribute("url").Value + "/metas.html");
            return View(vm);
        }

        //Blog
        public ActionResult Blog()
        {
            IEnumerable<XElement> entries = this.getXmlItemsListByType("BlogAllEntries");

            BlogViewModel vm = new BlogViewModel();
            vm.PageTitle = "Plastic Surgery Blog | Vanity Cosmetic Surgery";
            vm.entries = new List<BlogEntry>();

            foreach (XElement entry in entries)
            {
                BlogEntry be = this.createBlogEntry(entry);
                vm.entries.Add(be);
            }

            XElement blogElement = this.getXmlItemByType("Blog");

            vm.Meta = Server.MapPath("~/App_Data/Pages/En/" + blogElement.Attribute("url").Value + "/metas.html");

            return View(vm);
        }

        //Blog Single Entry
        public ActionResult BlogEntry(string entryId)
        {
            XElement elementBlogEntry = this.getXmlItemByType("BlogEntry", "", entryId);

            BlogEntry be = this.createBlogEntry(elementBlogEntry);
            be.PageTitle = be.Name + " | Vanity Cosmetic Surgery";

            return View(be);
        }

        //Regular Pages
        public ActionResult Page(string url)
        {
            BaseViewModel vm = new BaseViewModel();

            vm.Url = url;

            switch (url)
            {
                case "Index":
                    vm.PageTitle = "Cosmetic Surgery Miami | Top Pastic Surgeons - Vanity Cosmetic Surgery";
                    break;

                case "About":
                    vm.PageTitle = "About Us | Vanity Cosmetic Surgery";
                    break;

                case "Contact":
                    vm.PageTitle = "Contact Us | Vanity Cosmetic Surgery";
                    break;

                case "Blog":
                    vm.PageTitle = "Pastic Surgery Blog | Vanity Cosmetic Surgery";
                    break;

                default:
                    break;
            }

            vm.Meta = Server.MapPath("~/App_Data/Pages/En/" + url + "/metas.html").ToString();

            return View(vm);
        }

        //Procedures        
        public ActionResult Procedure(string url)
        {
            //Get Procedure element by its URL
            XElement procElement = this.getXmlItemByType("ProcedureByUrl", url);
            
            ProcedureViewModel vm = MyXmlParser.ParseProcedure(procElement);

            //Get all doctors that make a procedure with a specific ID
            IEnumerable<XElement> doctorElements = this.getXmlItemsListByType("MedicalStaff", vm.Id);

            List<MedicalStaff> doctors = new List<MedicalStaff>();
            
            foreach(var doctorElement in doctorElements)
                doctors.Add(MyXmlParser.ParseDoctor(doctorElement));

            vm.Doctors = doctors;

            vm.Article = Server.MapPath("~/App_Data/Articles/En/" + url + "/article.html").ToString();
            
            vm.Meta = Server.MapPath("~/App_Data/Articles/En/" + url + "/metas.html").ToString();

            vm.PageTitle = vm.Name + " | Vanity Cosmetic Surgery";

            return View(vm);
        }

        //Doctors        
        public ActionResult MedicalStaff(string url)
        {
            //Get a Doctor by his URL
            XElement doctorElement = this.getXmlItemByType("MedicalStaff", url);

            MedicalStaff vm = MyXmlParser.ParseDoctor(doctorElement);

            vm.Article = Server.MapPath("~/App_Data/Doctors/En/" + url + "/article.html").ToString();

            vm.Meta = Server.MapPath("~/App_Data/Doctors/En/" + url + "/metas.html").ToString();

            vm.PageTitle = "Dr. " + vm.Name + " | Vanity Cosmetic Surgery";

            return View(vm);
        }

        //Gallery
        public ActionResult Gallery()
        {
            //Get all procedures
            IEnumerable<XElement> procedures = this.getXmlItemsListByType("Procedures");

            GalleryViewModel vm = new GalleryViewModel();
            vm.PageTitle = "Plastic Surgery Before and After Pictures | Miami, Hialeah, Broward | Vanity Cosmetic Surgery";
            vm.Name = "Plastic Surgery Before and After pictures";
            vm.ImagesSets = new List<ImageSetViewModel>();
            
            foreach (XElement procedure in procedures)
            {
                //Get all images that belong to a procedure
                IEnumerable<XElement> procedureImageSet = this.getXmlItemsListByType("Gallery", procedure.Attribute("id").Value);

                if (procedureImageSet.Count<XElement>() == 0) continue;

                ImageSetViewModel imgSetVm = new ImageSetViewModel();
                imgSetVm.Id = procedure.Attribute("id").Value.ToString();
                imgSetVm.Name = procedure.Attribute("name").Value.ToString();
                imgSetVm.Images = this.getImagesFromProcedure(procedureImageSet);

                vm.ImagesSets.Add(imgSetVm);
            }
            vm.Meta = Server.MapPath("~/App_Data/Gallery/En/gallery/metas.html").ToString();

            return View(vm);
        }

        //Gallery by procedure
        public ActionResult GalleryProcedure(string procedureId)
        {
            int id = -1;
            if(!Int32.TryParse(procedureId, out id))
            {
                ViewBag.ErrorMessage = "The current param is not a vaild argument.";
                return View();
            }
            else
            {
                //Get all Doctors that perform a specific procedure
                IEnumerable<XElement> doctorElements = this.getXmlItemsListByType("DoctorsByProcedure", procedureId);

                GalleryViewModel vm = new GalleryViewModel();
                vm.Name = "Plastic Surgery Before and After pictures";
                vm.ImagesSets = new List<ImageSetViewModel>();

                foreach (XElement doctor in doctorElements)
                {
                    //Get all images that belong to a Procedure and to the current Doctor
                    IEnumerable<XElement> procedureImagesByDoctor = this.getXmlItemsListByType("ProcedureImagesByMedicalStaff", procedureId, doctor.Attribute("id").Value);

                    if (procedureImagesByDoctor.Count<XElement>() == 0) continue;

                    ImageSetViewModel imgSetVm = new ImageSetViewModel();
                    imgSetVm.Name = doctor.Attribute("name").Value;

                    if (procedureImagesByDoctor.Count<XElement>() > 4)
                        imgSetVm.IsCarousel = true;
                    else
                        imgSetVm.IsCarousel = false;

                    imgSetVm.Images = this.getImagesFromProcedure(procedureImagesByDoctor);
                    
                    vm.ImagesSets.Add(imgSetVm);
                }

                //Get the Procedure URL by Id
                string procedureUrl = this.getXmlItemByType("ProcedureById", "", procedureId).Attribute("url").Value;

                vm.Meta = Server.MapPath("~/App_Data/Gallery/En/" + procedureUrl + "/metas.html").ToString();

                return View(vm);
            }
        }

        //Protected methods
        public ActionResult DisplayFile(string FileFullName)
        {
            var file = FileFullName;
            return File(file, "text/html");
        }

        //Private methods
        private XElement getXmlItemByType(string type, string url = "", string id = "")
        {
            XDocument doc = XDocument.Load(Server.MapPath("~/Content/InitialData.xml"));
            XElement element = null;

            switch (type)
            {
                case "ProcedureByUrl":
                    element = doc.Root.Element("Procedures").Elements("Procedure").Where(
                        e => e.Attribute("url").Value == url).FirstOrDefault();
                    break;

                case "ProcedureById":
                    element = doc.Root.Element("Procedures").Elements("Procedure").Where(
                        e => e.Attribute("id").Value == id).FirstOrDefault();
                    break;

                case "MedicalStaff":
                    element = doc.Root.Element("MedicalStaffs").Elements("MedicalStaff").Where(
                        e => e.Attribute("url").Value == url).FirstOrDefault();
                    break;

                case "Blog":
                    element = doc.Root.Element("Blog");
                    break;

                case "HomePage":
                    element = doc.Root.Element("HomePage");
                    break;

                case "About":
                    element = doc.Root.Element("About");
                    break;

                case "Contact":
                    element = doc.Root.Element("Contact");
                    break;

                case "BlogEntry":
                    element = doc.Root.Element("Blog").Elements("BlogEntry").Where(
                        e => e.Attribute("id").Value == id).FirstOrDefault();
                    break;
            }

            return element;
        }

        private IEnumerable<XElement> getXmlItemsListByType(string elementType, string id = "", string mdID = "")
        {
            XDocument doc = XDocument.Load(Server.MapPath("~/Content/InitialData.xml"));
            IEnumerable<XElement> elements = null;

            switch (elementType)
            {
                case "Procedures":
                    elements = doc.Root.Element("Procedures").Elements("Procedure").ToList();
                    break;
                
                case "MedicalStaff":
                    elements = doc.Root.Element("MedicalStaffs").Elements("MedicalStaff").Where(
                        e => (e.Elements("Procedure").Any(p => p.Attribute("id").Value == id)));
                    break;

                case "Gallery":
                    elements = doc.Root.Element("Gallery").Elements("Image").Where(
                        e => e.Attribute("procedureid").Value == id).Take(4);
                    break;

                case "DoctorsByProcedure":
                    elements = doc.Root.Element("MedicalStaffs").Elements("MedicalStaff").Where(
                        e => (e.Elements("Procedure").Any(p => p.Attribute("id").Value == id)));
                    break;

                case "ProcedureImagesByMedicalStaff":
                    elements = doc.Root.Element("Gallery").Elements("Image").Where(
                        e => e.Attribute("procedureid").Value == id).Where(
                        d => d.Attribute("medicalstaffid").Value == mdID);
                    break;

                case "BlogAllEntries":
                    elements = doc.Root.Element("Blog").Elements("BlogEntry").ToList();
                    break;

               /* case "RelatedBlogEntries":
                    elements = doc.Root.Element("Blog").Elements("BlogEntry").Where(
                        e => e.Attribute("id").Value != id).ToList();
                    break;*/

                case "EntryComments":
                    elements = doc.Root.Element("Blog").Elements("Comment").Where(
                        c => c.Attribute("blogentryid").Value == id).ToList();
                    break;
            }

            return elements;
        }

        private BlogEntry createBlogEntry(XElement element)
        {
            BlogEntry be = new BlogEntry();
            be.Id = element.Attribute("id").Value;
            be.Name = element.Attribute("name").Value;
            be.Url = element.Attribute("url").Value;
            be.Thumb = element.Attribute("thumb").Value;
            be.Src = element.Attribute("src").Value;
            be.Date = DateTime.Parse(element.Attribute("date").Value.ToString());
            be.Author = element.Attribute("author").Value;
            be.BlogName = element.Attribute("blogname").Value;
            be.ShortText = element.Element("ShortText").Value;
            be.LongText = element.Element("LongText").Value;

            if ((element.NextNode as XElement) != null)
                be.NextId = (element.NextNode as XElement).Attribute("id").Value;
            else
                be.NextId = "-1";

            if ((element.PreviousNode as XElement) != null)
                be.PreviousId = (element.PreviousNode as XElement).Attribute("id").Value;
            else
                be.PreviousId = "-1";

            IEnumerable<XElement> commentElements = this.getXmlItemsListByType("EntryComments", be.Id);

            ICollection<CommetViewModel> entryComments = this.createEntryComments(commentElements);

            be.Comments = entryComments;

            return be;
        }

        private ICollection<CommetViewModel> createEntryComments(IEnumerable<XElement> list)
        {
            ICollection<CommetViewModel> comments = new List<CommetViewModel>();

            foreach (XElement comment in list)
            {
                CommetViewModel cvm = new CommetViewModel();
                cvm.Id = comment.Attribute("id").Value;
                cvm.BlogEntryId = comment.Attribute("blogentryid").Value;
                cvm.Date = DateTime.Parse(comment.Attribute("date").Value.ToString());
                cvm.Email = comment.Attribute("email").Value;
                cvm.WebSite = comment.Attribute("web").Value;
                cvm.Comment = comment.Value;

                comments.Add(cvm);
            }
            return comments;
        }
        
        private ICollection<GalleryImage> getImagesFromProcedure(IEnumerable<XElement> elements)
        {
            IList<GalleryImage> imagesByProcedure = new List<GalleryImage>();

            foreach (XElement image in elements)
            {
                GalleryImage vm = MyXmlParser.ParseGalleryImage(image);

                imagesByProcedure.Add(vm);
            }

            return imagesByProcedure;
        }
    }
}
