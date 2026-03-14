const roundCurrency = (value) => Math.round((Number(value || 0) + Number.EPSILON) * 100) / 100;

export const calculateBillTotals = (
    items = [],
    gstPercentage = 0,
    gstIsInclusive = false,
    serviceChargePercentage = 0,
    serviceChargeIsInclusive = false,
) => {
    const subtotalAmount = roundCurrency(
        items.reduce((sum, item) => sum + (Number(item.price || 0) * Number(item.quantity || 0)), 0),
    );
    const inclusiveRate = (gstIsInclusive ? Number(gstPercentage || 0) : 0)
        + (serviceChargeIsInclusive ? Number(serviceChargePercentage || 0) : 0);
    const baseAmount = inclusiveRate > 0
        ? roundCurrency(subtotalAmount / (1 + (inclusiveRate / 100)))
        : subtotalAmount;
    const gstAmount = roundCurrency(baseAmount * (Number(gstPercentage || 0) / 100));
    const serviceChargeAmount = roundCurrency(baseAmount * (Number(serviceChargePercentage || 0) / 100));
    const totalAmount = roundCurrency(
        subtotalAmount
        + (gstIsInclusive ? 0 : gstAmount)
        + (serviceChargeIsInclusive ? 0 : serviceChargeAmount),
    );

    return {
        subtotalAmount,
        gstAmount,
        serviceChargeAmount,
        totalAmount,
    };
};
