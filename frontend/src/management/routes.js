import NotFound from "@/components/Default/NotFound.vue";
import Home from "@/management/views/Main/Home.vue";
import AllProducts from "@/management/views/Product/AllProducts.vue";
import CategoriesBrands from "@/management/views/Product/CategoriesBrands.vue";
import CreateProduct from "@/management/views/Product/CreateProduct.vue";
import ProductPreview from "@/management/views/Product/ProductPreview.vue";
import Warehouse from "@/management/views/Warehouse/Warehouse.vue";
import Slider from "@/management/views/ContentManagement/Slider.vue";
import NewArrivals from "@/management/views/ContentManagement/NewArrivals.vue";
import EditProduct from "@/management/views/Product/EditProduct.vue";
import TrashedProducts from "@/management/views/Product/TrashedProducts.vue";
import Users from "@/management/views/Users/Users.vue";
import Guests from "@/management/views/Users/Guests.vue";

export default [
    {
        path: "",
        component: Home,
    },
    {
        path: "products/create",
        component: CreateProduct,
    },
    {
        path: "products",
        component: AllProducts,
        name: "product-list",
    },
    {
        path: "warehouse",
        component: Warehouse,
    },
    {
        path: "clients/regular",
        component: Users
    },
    {
        path: "clients/guests",
        component: Guests
    },
    {
        path: "categories-and-brands",
        component: CategoriesBrands,
        name: "categories-brands",
    },
    {
        path: "products/preview/:id/:slug",
        component: ProductPreview,
        props: true,
        name: "product-preview",
    },
    {
        path: "products/edit/:id/:slug",
        component: EditProduct,
        props: true,
        name: "product-edit",
    },
    {
        path: "products/trashed",
        component: TrashedProducts,
        name: "products-trashed"
    },
    {
        path: "content/slider",
        component: Slider,
        name: "content-slider",
    },
    {
        path: "content/new-arrivals",
        component: NewArrivals,
        name: "content-arrivals",
    },
    {
        path: "/:any(.*)*",
        component: NotFound,
        name: "not-found",
    },
];
