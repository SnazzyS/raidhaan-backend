const roundCurrency = (value) => Math.round((Number(value || 0) + Number.EPSILON) * 100) / 100;

export const calculateBillTotals = (
    items = [],
    discountType = null,
    discountValue = 0,
    gstPercentage = 0,
    gstIsInclusive = false,
    serviceChargePercentage = 0,
    serviceChargeIsInclusive = false,
) => {
    const subtotalAmount = roundCurrency(
        items.reduce((sum, item) => sum + (Number(item.price || 0) * Number(item.quantity || 0)), 0),
    );
    const normalizedDiscountType = discountType || null;
    const requestedDiscountAmount = normalizedDiscountType === 'percentage'
        ? roundCurrency(subtotalAmount * (Number(discountValue || 0) / 100))
        : normalizedDiscountType === 'fixed'
            ? roundCurrency(discountValue)
            : 0;
    const discountAmount = Math.min(requestedDiscountAmount, subtotalAmount);
    const discountedSubtotalAmount = roundCurrency(subtotalAmount - discountAmount);
    const inclusiveRate = (gstIsInclusive ? Number(gstPercentage || 0) : 0)
        + (serviceChargeIsInclusive ? Number(serviceChargePercentage || 0) : 0);
    const baseAmount = inclusiveRate > 0
        ? roundCurrency(discountedSubtotalAmount / (1 + (inclusiveRate / 100)))
        : discountedSubtotalAmount;
    const gstAmount = roundCurrency(baseAmount * (Number(gstPercentage || 0) / 100));
    const serviceChargeAmount = roundCurrency(baseAmount * (Number(serviceChargePercentage || 0) / 100));
    const totalAmount = roundCurrency(
        discountedSubtotalAmount
        + (gstIsInclusive ? 0 : gstAmount)
        + (serviceChargeIsInclusive ? 0 : serviceChargeAmount),
    );

    return {
        subtotalAmount,
        discountAmount,
        discountedSubtotalAmount,
        gstAmount,
        serviceChargeAmount,
        totalAmount,
    };
};
