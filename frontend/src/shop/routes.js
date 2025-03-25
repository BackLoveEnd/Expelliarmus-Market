import HomePage from "@/shop/views/Main/HomePage.vue";
import SignUp from "@/shop/views/Auth/SignUp.vue";
import LogIn from "@/shop/views/Auth/LogIn.vue";
import Wishlist from "@/shop/views/Order/Wishlist.vue";
import AboutUs from "@/shop/views/Main/AboutUs.vue";
import Contact from "@/shop/views/Main/Contact.vue";
import AccountSettings from "@/shop/views/Account/AccountSettings.vue";
import Cart from "@/shop/views/Order/Cart.vue";
import CheckOut from "@/shop/views/Order/Checkout.vue";
import Product from "@/shop/views/Product/Product.vue";
import CategoriesBrowse from "@/shop/views/Shop/CategoriesBrowse.vue";
import CategoriesOverview from "@/shop/views/Shop/CategoriesOverview.vue";

export default [
    {
        path: "/",
        component: HomePage,
        name: "home",
    },
    {
        path: "/sign-up",
        component: SignUp,
        meta: {
            guest: true,
        },
    },
    {
        path: "/log-in",
        component: LogIn,
        name: "login",
        meta: {
            guest: true,
        },
    },
    {
        path: "/shop/categories",
        component: CategoriesOverview,
        name: "categories-overview"
    },
    {
        path: "/shop/categories/:categorySlug",
        name: "categories-browse",
        props: ({name: null}),
        component: CategoriesBrowse,
    },
    {
        path: "/shop/products/:productSlug",
        name: "product-page",
        component: Product
    },
    {
        path: "/wishlist",
        component: Wishlist,
        meta: {
            requiresAuth: true,
        },
    },
    {
        path: "/cart",
        component: Cart,
    },
    {
        path: "/checkout",
        component: CheckOut,
    },
    {
        path: "/about-us",
        component: AboutUs,
    },
    {
        path: "/contact",
        component: Contact,
    },
    {
        path: "/account",
        component: AccountSettings,
        meta: {
            requiresAuth: true,
        },
    },
];
