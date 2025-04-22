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
import AllProducts from "@/shop/views/Product/AllProducts.vue";
import BrandsOverview from "@/shop/views/Shop/BrandsOverview.vue";
import BrandsBrowse from "@/shop/views/Shop/BrandsBrowse.vue";
import {useCartStore} from "@/stores/useCartStore.js";

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
        path: "/shop/products",
        component: AllProducts,
        name: "all-products"
    },
    {
        path: "/shop/categories",
        component: CategoriesOverview,
        name: "categories-overview"
    },
    {
        path: '/shop/brands',
        component: BrandsOverview,
        name: "brands-overview",
    },
    {
        path: '/shop/brands/:brandSlug',
        component: BrandsBrowse,
        name: 'brands-browse',
    },
    {
        path: "/shop/categories/:categorySlug",
        name: "categories-browse",
        props: true,
        component: CategoriesBrowse,
    },
    {
        path: "/shop/products/:productId/:productSlug",
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
        name: "cart"
    },
    {
        path: "/checkout",
        component: CheckOut,
        beforeEnter(to, from, next) {
            const cart = useCartStore();

            if (cart.totalItems === 0) {
                return next({name: "cart"});
            }

            return next();
        }
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
