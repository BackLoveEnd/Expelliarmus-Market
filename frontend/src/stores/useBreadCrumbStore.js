import {defineStore} from "pinia";

export const useBreadCrumbStore = defineStore("breadcrumb", {
    state: () => ({
        breadcrumbs: [],
    }),
    actions: {
        setBreadcrumbs(newBreadcrumbs) {
            this.breadcrumbs = newBreadcrumbs;
        },
        addBreadcrumb(breadcrumb) {
            const indexOfExistBreadcrumb = this.breadcrumbs.findIndex(
                (item) => item.name === breadcrumb.name
            );

            if (indexOfExistBreadcrumb === -1) {
                this.breadcrumbs.push(breadcrumb);
            } else {
                this.breadcrumbs = this.breadcrumbs.slice(0, indexOfExistBreadcrumb + 1);
            }
        },
        clearBreadcrumbs() {
            this.breadcrumbs = [];
        },
    },
    persist: true
});
