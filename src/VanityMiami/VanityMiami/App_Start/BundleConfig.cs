using System.Web;
using System.Web.Optimization;

namespace VanityMiami
{
    public class BundleConfig
    {
        // For more information on Bundling, visit http://go.microsoft.com/fwlink/?LinkId=254725
        public static void RegisterBundles(BundleCollection bundles)
        {
            bundles.Add(new ScriptBundle("~/Content/themes/avada/js/generalsScripts1").Include(
                                    "~/Content/themes/avada/js/jquery.showmore.js").Include(
                                    "~/Content/themes/avada/js/jquery.dropdown.js").Include(
                                    "~/Content/themes/avada/js/jquery.jigowatt.js").Include(
                                    "~/Content/themes/avada/js/owl.carousel.js"));

            bundles.Add(new ScriptBundle("~/Content/themes/avada/js/bundles/webfont").Include(
                                    "~/Content/themes/avada/js/bundles/webfont.js"));
            
            bundles.Add(new ScriptBundle("~/Content/themes/avada/js/jackbox/jackboxScripts").Include(
                                                "~/Content/themes/avada/js/jackbox/jackboxOptions.js",
                                                "~/Content/themes/avada/js/jackbox/jackbox-scripts.js"));

            bundles.Add(new ScriptBundle("~/Content/themes/avada/js/commentReply").Include(
                                                "~/Content/themes/avada/js/jackbox/comment-reply.min.js"));

            bundles.Add(new ScriptBundle("~/Content/themes/avada/js/bundles/ScriptBlock1").Include(
                                                "~/Content/themes/avada/js/bundles/ScriptBlock1.js"));

            bundles.Add(new ScriptBundle("~/Content/themes/avada/js/google/analyticsScript").Include(
                                                "~/Content/themes/avada/js/google/analytics.js"));

            bundles.Add(new ScriptBundle("~/Content/themes/avada/js/bundles/ScriptBlock2").Include(
                                                "~/Content/themes/avada/js/bundles/ScriptBlock2.js"));

            bundles.Add(new ScriptBundle("~/Content/themes/avada/js/bundles/owlrooms").Include(
                                              "~/Content/themes/avada/js/bundles/owlrooms.js"));
            
            bundles.Add(new ScriptBundle("~/Content/themes/avada/js/generalsScripts3").Include(
                                    "~/Content/themes/avada/js/modernizr-min.js").Include(
                                    "~/Content/themes/avada/js/jquery.carouFredSel-6.2.1-min.js").Include(
                                    "~/Content/themes/avada/js/jquery.prettyPhoto-min.js").Include(
                                    "~/Content/themes/avada/js/jquery.flexslider-min.js").Include(
                                    "~/Content/themes/avada/js/jquery.fitvids-min.js"));

            bundles.Add(new ScriptBundle("~/Content/themes/avada/js/main-min").Include(
                                                 "~/Content/themes/avada/js/main-min.js"));
                        
            bundles.Add(new ScriptBundle("~/Content/themes/avada/js/e-201440").Include(
                                                "~/Content/themes/avada/js/e-201440.js"));

            bundles.Add(new ScriptBundle("~/Content/themes/avada/js/ScriptBlock3").Include(
                                                 "~/Content/themes/avada/js/bundles/ScriptBlock3.js"));

            // Use the development version of Modernizr to develop with and learn from. Then, when you're
            // ready for production, use the build tool at http://modernizr.com to pick only the tests you need.

            bundles.Add(new StyleBundle("~/Content/themes/avada/css/generalsStyles1").Include(
                "~/Content/themes/avada/css/font-awesome.css",
                "~/Content/themes/avada/css/owl.carousel.css",
                "~/Content/themes/avada/css/owl.theme.css",
                "~/Content/themes/avada/css/jquery.dropdown.css",
                "~/Content/themes/avada/css/button-alt.css",
                "~/Content/themes/avada/css/tooltipster.css",
                "~/Content/themes/avada/css/GoogleLikeForms.css",
                "~/Content/themes/avada/css/googlecustomforms.css",
                "~/Content/themes/avada/css/buttonPro.css",
                "~/Content/themes/avada/css/magnific-popup.css"));

            

            bundles.Add(new StyleBundle("~/Content/themes/avada/css/generalsStyles2").Include(
                "~/Content/themes/avada/css/style.css",
                "~/Content/themes/avada/css/media.css",
                "~/Content/themes/avada/css/animate-custom.css",
                "~/Content/themes/avada/css/author_hreview/style.css",
                "~/Content/themes/avada/css/revslider/rs-plugin/settings.css",
                "~/Content/themes/avada/css/revslider/rs-plugin/captions.css",
                "~/Content/themes/avada/css/jackbox/jackbox-global.css"));

            bundles.Add(new StyleBundle("~/Content/themes/avada/css/bundles/StyleBlock1").Include(
                            "~/Content/themes/avada/css/bundles/StyleBlock1.css"));
            
            bundles.Add(new StyleBundle("~/Content/themes/avada/css/bundles/StyleBlock2").Include(
                            "~/Content/themes/avada/css/bundles/StyleBlock2.css"));

            bundles.Add(new StyleBundle("~/Content/themes/avada/css/bundles/StyleBlock3").Include(
                            "~/Content/themes/avada/css/bundles/StyleBlock3.css"));

            bundles.Add(new StyleBundle("~/Content/themes/avada/css/bundles/StyleBlock4").Include(
                            "~/Content/themes/avada/css/bundles/StyleBlock4.css"));
        }
    }
}