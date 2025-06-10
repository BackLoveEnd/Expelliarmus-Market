import {reactive, ref} from "vue";
import {CategoryService} from "@/services/Category/CategoryService.js";

export function useCategoryManager() {
    const nodes = ref([]);
    const isModalOpen = ref(false);
    const currentNode = ref(null);
    const newCategory = ref({name: "", slug: ""});
    const modalAction = ref({
        categoryId: null,
        title: "",
        btnName: "",
        icon: null,
        handler: null,
    });
    const categoryAttributes = ref([]);

    const actionEvent = reactive({
        type: "",
        payload: null,
    });

    async function loadCategories() {
        await CategoryService.getCategoriesTree()
            .then((response) => {
                nodes.value = transformCategories(response.data.data);
            })
            .catch((e) => {
            });
    }

    async function fetchAttributesForCategory(categoryId) {
        try {
            if (categoryId) {
                const response =
                    await CategoryService.getAttributesForCategory(categoryId);
                categoryAttributes.value = response?.data?.data || [];
            } else {
                categoryAttributes.value = [];
            }
        } catch (error) {
        }
    }

    function transformCategories(data, parentKey = "") {
        return data.map((item, index) => {
            const currentKey = parentKey ? `${parentKey}-${index}` : String(index);
            return {
                key: currentKey,
                data: {...item},
                children: item.children?.length
                    ? transformCategories(item.children, currentKey)
                    : [],
            };
        });
    }

    function openModalToAddNewCategory() {
        currentNode.value = null;
        modalAction.value = {
            categoryId: null,
            title: "Create New Category",
            btnName: "Create",
            icon: null,
            handler: addNewCategory,
        };
        isModalOpen.value = true;
    }

    async function openModalToAddSubcategory(node) {
        currentNode.value = node;
        modalAction.value = {
            categoryId: node.data.id,
            title: `Add subcategory for ${node.data.name}`,
            btnName: "Add",
            icon: node.data.icon,
            handler: addSubcategory,
        };
        await fetchAttributesForCategory(node.data.id);
        isModalOpen.value = true;
    }

    async function openModalToEditCategory(node) {
        currentNode.value = node;
        modalAction.value = {
            categoryId: node.data.id,
            title: `Edit Category: ${node.data.name}`,
            btnName: "Edit",
            icon: node.data.icon,
            handler: saveCategoryChanges,
        };
        newCategory.value.name = node.data.name;
        await fetchAttributesForCategory(node.data.id);
        isModalOpen.value = true;
    }

    function closeModal() {
        isModalOpen.value = false;
        newCategory.value = {name: "", slug: ""};
        categoryAttributes.value = [];
    }

    function isFirstNode(node) {
        const parsed = parseInt(node.key, 10);
        return Number.isInteger(parsed) && parsed.toString() === node.key;
    }

    function addSubcategory() {
        if (!newCategory.value.name.trim()) {
            alert("Category name is required.");
            return;
        }

        const newKey = addCategoryToTree(currentNode.value?.key);

        actionEvent.type = "add";
        actionEvent.payload = {
            parent: currentNode.value ? currentNode.value.data.id : null,
            name: newCategory.value.name,
            key: newKey,
        };
        closeModal();
    }

    function addNewCategory() {
        if (!newCategory.value.name.trim()) {
            alert("Category name is required.");
            return;
        }

        const newKey = addCategoryToTree(nodes.value.length);

        actionEvent.type = "add";
        actionEvent.payload = {
            parent: null,
            name: newCategory.value.name,
            key: newKey,
        };

        closeModal();
    }

    function saveCategoryChanges() {
        if (!newCategory.value.name.trim()) {
            alert("Category name is required.");
            return;
        }

        currentNode.value.data.name = newCategory.value.name;

        actionEvent.type = "edit";
        actionEvent.payload = {
            id: currentNode.value.data.id,
            name: newCategory.value.name,
        };

        closeModal();
    }

    function addCategoryToTree(key) {
        const newKey = currentNode.value
            ? `${key}-${currentNode.value.children.length}`
            : `${nodes.value.length}`;

        const newNode = {
            key: newKey,
            data: {
                name: newCategory.value.name,
                slug: newCategory.value.name.toLowerCase().replace(/\s+/g, "-"),
            },
            children: [],
        };

        if (currentNode.value) {
            currentNode.value.children = currentNode.value.children || [];
            currentNode.value.children.push(newNode);
        } else {
            nodes.value.push(newNode);
        }

        return newKey;
    }

    function initializeNodeDeletion(node) {
        if (confirm(`Are you sure you want to delete ${node.data.name}?`)) {
            actionEvent.type = "delete";
            actionEvent.payload = {
                id: node.data.id,
                parent: currentNode.value ? currentNode.value.data.id : null,
                name: node.data.name,
                key: node.key,
            };
        }
    }

    function updateNodeId(nodesList, nodeKey, id) {
        nodesList.some((node) => {
            if (node.key === nodeKey) {
                node.data.id = id;
                return true;
            }

            if (node.children && node.children.length > 0) {
                return updateNodeId(node.children, nodeKey, id);
            }

            return false;
        });
    }

    function deleteNodeRecursively(nodesList, targetNode) {
        const index = nodesList.findIndex((node) => node.key === targetNode.key);
        if (index !== -1) {
            nodesList.splice(index, 1);
        } else {
            nodesList.forEach((node) => {
                if (node.children.length > 0) {
                    deleteNodeRecursively(node.children, targetNode);
                }
            });
        }
    }

    return {
        nodes,
        isModalOpen,
        newCategory,
        modalAction,
        actionEvent,
        categoryAttributes,
        updateNodeId,
        deleteNodeRecursively,
        isFirstNode,
        openModalToAddNewCategory,
        openModalToAddSubcategory,
        openModalToEditCategory,
        initializeNodeDeletion,
        closeModal,
        loadCategories,
    };
}
