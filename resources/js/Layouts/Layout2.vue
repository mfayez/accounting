<template>
<div :dir="RightToLeft" class="font-roboto">
	<navbars-with-avatar />
	<sidebar-with-search />
	<footers-with-details />
</div>
</template>

<script>
// Application components
import HeaderComponent from "@/components/Header";
import AppButton from "@/components/utilities/AppButton.vue";

// Alerts
import AlertsSuccessPop from "@/components/ui/Alerts/SuccessPop";
import AlertsInfoPop from "@/components/ui/Alerts/InfoPop";
import AlertsWarningPop from "@/components/ui/Alerts/WarningPop";
import AlertsErrorPop from "@/components/ui/Alerts/ErrorPop";
import AlertsNotificationPop from "@/components/ui/Alerts/NotificationPop";
import AlertsSuccessFullWidth from "@/components/ui/Alerts/SuccessFullWidth";
import AlertsInfoFullWidth from "@/components/ui/Alerts/InfoFullWidth";
import AlertsWarningFullWidth from "@/components/ui/Alerts/WarningFullWidth";
import AlertsErrorFullWidth from "@/components/ui/Alerts/ErrorFullWidth";
// Authentication
import AuthenticationSimpleLogin from "@/components/ui/Authentication/SimpleLogin";
import AuthenticationLoginWithSocailMediaLinks from "@/components/ui/Authentication/LoginWithSocailMediaLinks";
import AuthenticationLoginWithSideImage from "@/components/ui/Authentication/LoginWithSideImage";
// Buttons
import ButtonsPrimary from "@/components/ui/Buttons/Primary";
import ButtonsWithIcon from "@/components/ui/Buttons/WithIcon";
import ButtonsWithMenu from "@/components/ui/Buttons/WithMenu";
// Cards
import CardsArticle from "@/components/ui/Cards/Article";
import CardsArticleWithImage from "@/components/ui/Cards/ArticleWithImage";
import CardsArticleWithSMIcons from "@/components/ui/Cards/ArticleWithSMIcons";
import CardsProduct from "@/components/ui/Cards/Product";
import CardsProductWithEvaluation from "@/components/ui/Cards/ProductWithEvaluation";
import CardsSimpleProduct from "@/components/ui/Cards/SimpleProduct";
import CardsTestimonial from "@/components/ui/Cards/Testimonial";
import CardsUser from "@/components/ui/Cards/User";
import CardsUserWithDetails from "@/components/ui/Cards/UserWithDetails";
// Dropdowns
import DropdownsSimple from "@/components/ui/Dropdowns/Simple";
import DropdownsNotification from "@/components/ui/Dropdowns/Notification";
// Forms
import FormsNewsletter from "@/components/ui/Forms/Newsletter";
import FormsSimpleNewsletter from "@/components/ui/Forms/SimpleNewsletter";
import FormsSimple from "@/components/ui/Forms/Simple";
import FormsSearch from "@/components/ui/Forms/Search";
import FormsContact from "@/components/ui/Forms/Contact";
// Navbars
import NavbarsSimple from "@/components/ui/Navbars/Simple";
import NavbarsECommerce from "@/components/ui/Navbars/ECommerce";
import NavbarsWithAvatar from "@/components/ui/Navbars/WithAvatar";
import NavbarsWithSearch from "@/components/ui/Navbars/WithSearch";
import NavbarsWithCategories from "@/components/ui/Navbars/WithCategories";
import NavbarsWithSocailMediaLinks from "@/components/ui/Navbars/WithSocailMediaLinks";
// Heros
import HerosECommerce from "@/components/ui/Heros/ECommerce";
import HerosWithNewsletterForm from "@/components/ui/Heros/WithNewsletterForm";
import HerosWithSlide from "@/components/ui/Heros/WithSlide";
import HerosWithImage from "@/components/ui/Heros/WithImage";
import HerosWithSideImage from "@/components/ui/Heros/WithSideImage";
import HerosWithBGImage from "@/components/ui/Heros/WithBGImage";
import HerosWithPattern from "@/components/ui/Heros/WithPattern";
// Sidebars
import SidebarWithSearch from "@/components/ui/Sidebar/WithSearch";
import SidebarWithAvatar from "@/components/ui/Sidebar/WithAvatar";
// Sections
import SectionsAboutMe from "@/components/ui/Sections/AboutMe";
import SectionsFeature from "@/components/ui/Sections/Feature";
import SectionsOurTeam from "@/components/ui/Sections/OurTeam";
import SectionsParagraphWithImage from "@/components/ui/Sections/ParagraphWithImage";
// Pricing
import PricingSimple from "@/components/ui//Pricing/Simple";
import PricingPopular from "@/components/ui//Pricing/Popular";
import PricingWithNavigation from "@/components/ui//Pricing/WithNavigation";
// paginations
import PaginationSimple from "@/components/ui/Pagination/Simple";
// Footers
import FootersSimple from "@/components/ui/Footers/Simple";
import FootersWithDetails from "@/components/ui/Footers/WithDetails";
import FootersWithLinks from "@/components/ui/Footers/WithLinks";
import FootersWithSubscribeForm from "@/components/ui/Footers/WithSubscribeForm";

// Components
import Component from "@/models/ComponentsFilter";

export default {
    components: {
        AppButton,
        HeaderComponent,
        AlertsSuccessPop,
        AlertsInfoPop,
        AlertsWarningPop,
        AlertsErrorPop,
        AlertsNotificationPop,
        AlertsSuccessFullWidth,
        AlertsInfoFullWidth,
        AlertsWarningFullWidth,
        AlertsErrorFullWidth,
        AuthenticationSimpleLogin,
        AuthenticationLoginWithSocailMediaLinks,
        AuthenticationLoginWithSideImage,
        ButtonsPrimary,
        ButtonsWithIcon,
        ButtonsWithMenu,
        CardsArticle,
        CardsArticleWithImage,
        CardsArticleWithSMIcons,
        CardsProduct,
        CardsProductWithEvaluation,
        CardsSimpleProduct,
        CardsTestimonial,
        CardsUser,
        CardsUserWithDetails,
        DropdownsSimple,
        DropdownsNotification,
        FormsNewsletter,
        FormsSimpleNewsletter,
        FormsSimple,
        FormsSearch,
        FormsContact,
        NavbarsSimple,
        NavbarsECommerce,
        NavbarsWithAvatar,
        NavbarsWithSearch,
        NavbarsWithSocailMediaLinks,
        NavbarsWithCategories,
        HerosECommerce,
        HerosWithNewsletterForm,
        HerosWithSlide,
        HerosWithImage,
        HerosWithSideImage,
        HerosWithBGImage,
        HerosWithPattern,
        SidebarWithAvatar,
        SidebarWithSearch,
        SectionsAboutMe,
        SectionsFeature,
        SectionsOurTeam,
        SectionsParagraphWithImage,
        PricingSimple,
        PricingPopular,
        PricingWithNavigation,
        PaginationSimple,
        FootersSimple,
        FootersWithDetails,
        FootersWithLinks,
        FootersWithSubscribeForm,
    },
    
    data() {
        return {
			RightToLeft: "" 
        };
    },

    created() {
		if (this.$page.props.locale == "en")
			this.RightToLeft =  "ltr";
		else
			this.RightToLeft = "rtl";
    },

    computed: {
        list() {
        },
    },
};
</script>
