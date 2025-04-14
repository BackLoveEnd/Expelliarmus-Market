import {isObject} from "@vueuse/core";

export function useJsonApiFormatter() {
    function toJsonApi(data, type, relationships = {}) {
        const result = {
            data: {
                type: type,
                attributes: {...data},
            },
        };

        if (Object.keys(relationships).length > 0) {
            result.data.relationships = {};

            for (const [key, value] of Object.entries(relationships)) {
                if (value && typeof value === "object" && !Array.isArray(value)) {
                    result.data.relationships[key] = {
                        data: {...value},
                    };
                } else if (Array.isArray(value)) {
                    result.data.relationships[key] = {
                        data: value.map((item) => ({...item})),
                    };
                }
            }

            if (Object.keys(result.data.relationships).length === 0) {
                delete result.data.relationships;
            }
        }

        return JSON.stringify(result);
    }

    function fromJsonApiErrorsFields(errors) {
        const formattedErrors = [];

        Object.keys(errors).forEach((key) => {
            const errorMessages = errors[key];

            let formattedKey = key
                .replace(/(\.data|data\.)/g, "")
                .replace(/\.(\d+)\./g, "[$1].")
                .replace(/relationships\s/gi, "");

            errorMessages.forEach((message) => {
                formattedErrors.push({[formattedKey]: message});
            });
        });

        return formattedErrors;
    }

    function includedByRelationships(included, relationship) {
        return included.filter((include) => include.type === relationship);
    }

    function parseIncluded(data) {
        if (!data.data || !data.data.relationships) {
            throw new Error("Invalid data structure");
        }

        const result = {};

        Object.entries(data.data.relationships).forEach(([key, relationData]) => {
            const relationType = Array.isArray(relationData.data)
                ? relationData.data[0]?.type
                : relationData.data?.type;

            result[key] = data.included.filter(
                (include) => include.type === relationType,
            );
        });

        return result;
    }

    function formatUrl(url, params, trashed = null) {
        let queryParams = [];

        if (params?.page) {
            queryParams.push(`page=${params.page}`);
        }

        if (params?.offset) {
            queryParams.push(`offset=${params.offset}`);
        }

        if (params?.limit) {
            queryParams.push(`limit=${params.limit}`);
        }

        if (params?.sort) {
            if (Array.isArray(params.sort) && params.sort.length > 0) {
                const sortFields = params.sort.map(s => (s.order === "desc" ? "-" : "") + s.field).join(",");
                queryParams.push(`sort=${sortFields}`);
            } else if (params.sort?.field) {
                const sortPrefix = params.sort.order === "desc" ? "-" : "";
                queryParams.push(`sort=${sortPrefix}${params.sort.field}`);
            }
        }

        if (params?.filter) {
            Object.keys(params.filter).forEach((key) => {
                if (isObject(params.filter[key])) {
                    Object.keys(params.filter[key]).forEach((subKey) => {
                        queryParams.push(`filter[${key}][${subKey}]=${encodeURIComponent(params.filter[key][subKey])}`);
                    });
                } else {
                    queryParams.push(`filter[${key}]=${encodeURIComponent(params.filter[key])}`);
                }
            });
        }

        if (trashed !== null) {
            queryParams.push(`filter[trashed]=${trashed}`);
        }

        return url + (queryParams.length ? `?${queryParams.join("&")}` : "");
    }

    return {
        toJsonApi,
        fromJsonApiErrorsFields,
        formatUrl,
        includedByRelationships,
        parseIncluded,
    };
}
