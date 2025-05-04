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
import Discounts from "@/management/views/Warehouse/Discounts.vue";
import Managers from "@/management/views/Manager/Managers.vue";
import Coupons from "@/management/views/Users/Coupons.vue";
import PendingOrders from "@/management/views/Orders/PendingOrders.vue";
import CancelledOrders from "@/management/views/Orders/CancelledOrders.vue";
import DeliveredOrders from "@/management/views/Orders/DeliveredOrders.vue";
import RefundedOrders from "@/management/views/Orders/RefundedOrders.vue";

export default [
    {
        path: "",
        component: Home,
        name: "manager-home",
    },
    {
        path: "products/create",
        component: CreateProduct,
        name: "create-product"
    },
    {
        path: "products",
        component: AllProducts,
        name: "product-list",
    },
    {
        path: "warehouse",
        component: Warehouse,
        name: "warehouse"
    },
    {
        path: "clients/regular",
        component: Users,
        name: "regular-customers"
    },
    {
        path: "clients/guests",
        component: Guests,
        name: "guests"
    },
    {
        path: "clients/coupons",
        component: Coupons,
        name: "coupons"
    },
    {
        path: "products/discounts",
        component: Discounts,
        name: "discounts"
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
        path: "orders/pending",
        component: PendingOrders,
        name: "pending-orders"
    },
    {
        path: "orders/cancelled",
        component: CancelledOrders,
        name: "cancelled-orders"
    },
    {
        path: "orders/delivered",
        component: DeliveredOrders,
        name: "delivered-orders"
    },
    {
        path: "orders/refunded",
        component: RefundedOrders,
        name: "refunded-orders"
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
        path: "managers",
        component: Managers,
        name: "managers",
        meta: {
            onlySuperManager: true
        }
    },
    {
        path: "/management/:any(.*)*",
        component: NotFound,
        name: "not-found",
    },
];
