import { createPortal } from 'react-dom';

export default function Modal({ isOpen, onClose, title, children }) {
    if (!isOpen) return null;

    return createPortal(
        <div className="fixed inset-0 z-50 overflow-y-auto">
            <div className="flex min-h-full items-center justify-center p-4">
                {/* Backdrop */}
                <div className="fixed inset-0 bg-gray-900/50" onClick={onClose} />

                {/* Modal */}
                <div className="relative w-full max-w-md rounded-lg border border-gray-200 bg-white shadow-xl">
                    {/* Header */}
                    <div className="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                        <h3 className="text-sm font-medium text-gray-900">{title}</h3>
                        <button onClick={onClose} className="text-gray-400 hover:text-gray-600">
                            <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {/* Content */}
                    <div className="p-4">
                        {children}
                    </div>
                </div>
            </div>
        </div>,
        document.body
    );
}

function ModalFooter({ children }) {
    return (
        <div className="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-200">
            {children}
        </div>
    );
}

Modal.Footer = ModalFooter;
