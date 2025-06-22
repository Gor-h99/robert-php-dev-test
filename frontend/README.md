# Robert CAT Tool - Frontend

This is the React frontend for the Robert Computer-Assisted Translation (CAT) tool, built with modern React patterns and a clean architecture.

## Features

- Display list of translation units
- Add new translation units
- Edit existing translations
- Delete translation units
- Responsive design for mobile and desktop
- Modern UI with loading states and error handling
- Form validation and user feedback

## Architecture

The frontend follows a clean, modular architecture with clear separation of concerns:

```
frontend/src/
├── components/          # Reusable UI components
│   ├── TranslationForm.jsx
│   ├── TranslationList.jsx
│   ├── TranslationItem.jsx
│   ├── LoadingSpinner.jsx
│   └── ErrorMessage.jsx
├── hooks/              # Custom React hooks
│   ├── useTranslations.js
│   └── useTranslationForm.js
├── services/           # API and external services
│   └── api.js
├── App.jsx             # Main application component
├── main.jsx            # Application entry point
└── index.css           # Global styles
```

### Architecture Principles

1. **Separation of Concerns**: Each layer has a specific responsibility
2. **Reusability**: Components and hooks are designed to be reusable
3. **Maintainability**: Clean code structure for easy maintenance
4. **Testability**: Components and hooks are easily testable
5. **Scalability**: Architecture supports future growth

## Getting Started

### Prerequisites

- Node.js (version 14 or higher)
- npm or yarn

### Installation

1. Install dependencies:
```bash
npm install
```

2. Start the development server:
```bash
npm run dev
```

3. Open your browser and navigate to the URL shown in the terminal (usually http://localhost:5173)

### Building for Production

To build the application for production:

```bash
npm run build
```

The built files will be in the `dist` directory.

## Project Structure

### Components (`/components`)

Reusable UI components that handle presentation logic:

- **`TranslationForm.jsx`** - Form for adding new translation units
- **`TranslationList.jsx`** - Container for displaying translation units
- **`TranslationItem.jsx`** - Individual translation unit row
- **`LoadingSpinner.jsx`** - Reusable loading spinner
- **`ErrorMessage.jsx`** - Reusable error message component

### Hooks (`/hooks`)

Custom React hooks for state management and business logic:

- **`useTranslations.js`** - Manages translation state and API operations
- **`useTranslationForm.js`** - Manages form state and validation

### Services (`/services`)

External service integrations and API communication:

- **`api.js`** - API service for backend communication

## API Integration

The frontend communicates with the PHP API located at `/api/translations.php`. The `ApiService` class handles all API operations:

- `fetchTranslations()` - Get all translation units
- `fetchTranslationById(id)` - Get specific translation unit
- `createTranslation(data)` - Create new translation unit
- `updateTranslation(id, data)` - Update existing translation unit
- `deleteTranslation(id)` - Delete translation unit

## State Management

The application uses React hooks for state management:

- **`useTranslations`** - Global translation state and operations
- **`useTranslationForm`** - Form state and validation
- **Local State** - Component-specific state (editing, loading, etc.)

## Styling

The application uses CSS3 with:
- Responsive design for all device sizes
- Modern UI components with hover effects
- Loading animations and transitions
- Error states and form validation styling

## Available Scripts

- `npm run dev` - Start development server
- `npm run build` - Build for production
- `npm run preview` - Preview production build

## Development Guidelines

### Component Development

1. **Use .jsx extension** for all component files
2. **Follow functional component pattern** with hooks
3. **Implement proper prop validation** with JSDoc comments
4. **Keep components focused** on a single responsibility
5. **Use semantic HTML** for accessibility

### Hook Development

1. **Use descriptive names** for custom hooks
2. **Return consistent interfaces** from hooks
3. **Handle errors gracefully** in hooks
4. **Use useCallback and useMemo** for performance optimization
5. **Document hook parameters and return values**

### Service Development

1. **Centralize API calls** in service classes
2. **Handle errors consistently** across all API calls
3. **Use proper HTTP methods** for different operations
4. **Implement request/response interceptors** if needed
5. **Document service methods** with JSDoc

## Testing

The architecture is designed to be easily testable:

- **Components** can be tested in isolation
- **Hooks** can be tested with React Testing Library
- **Services** can be mocked for unit testing
- **Integration tests** can test the full flow

## Future Enhancements

- **TypeScript** integration for better type safety
- **State management library** (Redux/Zustand) for complex state
- **Component library** for consistent UI components
- **Storybook** for component documentation
- **Unit tests** with Jest and React Testing Library
- **E2E tests** with Cypress or Playwright

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Performance Considerations

- **Code splitting** with React.lazy for route-based splitting
- **Memoization** with React.memo for expensive components
- **Optimized re-renders** with useCallback and useMemo
- **Bundle optimization** with Vite's built-in optimizations 